#!/usr/bin/python
# -*- coding: utf-8 -*-
import csv
import os
import re
import StringIO
import urllib2

from pyquery import PyQuery as pq
from utils import UnicodeWriter

nasdaq_urls = [
	'http://en.wikipedia.org/wiki/Category:Companies_listed_on_NASDAQ',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pagefrom=Cisco+Systems#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pagefrom=Gravity+%28company%29#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pagefrom=Morgans+Hotel+Group#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pagefrom=Sapient+Corporation#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pageuntil=West+Marine#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_NASDAQ&pagefrom=West+Marine#mw-pages',
]

nyse_urls = [
	'http://en.wikipedia.org/wiki/Category:Companies_listed_on_the_New_York_Stock_Exchange',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Blyth%2C+Inc.#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Covidien#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Gazit-Globe#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=JPMorgan+Chase#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=MSCI#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Quicksilver+Resources#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Talbots#mw-pages',
	'http://en.wikipedia.org/w/index.php?title=Category:Companies_listed_on_the_New_York_Stock_Exchange&pagefrom=Weis+Markets#mw-pages',
]

amex_urls = [
	'http://en.wikipedia.org/wiki/Category:Companies_listed_on_the_American_Stock_Exchange',
]

market = [
	'NASDAQ',
	'NYSE',
	'AMEX',
]

def company_wiki_link_generator(urls):
	'''
	產生公司的維基百科連結
	'''
	start = '<li><a href=\"'
	end = '\" title'
	for url in urls:
		response = get_web_contents(url)

		matched = cut_between(start, end, response)
		matched = matched[:-3]
		for link in matched:
			company_url = 'http://en.wikipedia.org' + link
			yield company_url

def get_web_contents(url):
	req = urllib2.Request(url, None, {'User-agent' : 'Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5'})
	response = urllib2.urlopen(req).read()
	
	return response

def cut_between(start, end, content):
	#用正規表示法擷取以start為開頭，end為結尾的字串陣列
	
	matched = []
	for m in re.finditer("%s(?P<test>.*?)%s" % (start, end), content):
		matched.append(m.group('test'))
	return matched


def get_company_attr(market, urls, output_file):
	col_headers = [	#維基百科上原本就有的欄位
    	'Industry',
    	'Products',
    	'Type',
    	'Traded as',
    	'Area served',
    	'Parent',
    	'Subsidiaries',
    	'Key people',
    	'Genre',
    	'Services',
    	'Owner(s)',
    	'Employees',
    	'Website',
    	'Logo',
    ]
	extra_col_headers = [#經過處理後產生的欄位
		'Symbol',
	]
	csv_writer = UnicodeWriter(open(output_file, 'wb'), delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL, encoding='utf-8')
	csv_writer.writerow(['Wiki link']+col_headers+extra_col_headers)
	
	
	re_obj = re.compile("%s : (?P<symbol>\w*)" % market)
	
	for url in company_wiki_link_generator(urls):	#每個公司的wiki頁面
		print url
		response = get_web_contents(url)
		d = pq(response)
		p = d(".infobox")
		infobox = p.html()	#取出公司資料的table
		
		d = pq(infobox)
		rows = d.find('tr')

		data = [None]*(len(col_headers)+len(extra_col_headers))
		symbol = ''
		for row in rows:	#公司資料table中的每一列
			th_txt = pq(row).find('th').text()
			td = pq(row).find('td')
			if td.attr('class') == 'logo':
				td_txt = td.find('img').attr('src')
				data[col_headers.index('Logo')] = td_txt
			if th_txt in col_headers:	
				td_txt = td.text()
				data[col_headers.index(th_txt)] = td_txt
			
			#從Wiki的Type或Trade as欄位取的股票的Symbol
			
			if th_txt == 'Type':
				try:
					symbol = re_obj.search(td_txt).group("symbol")
				except Exception:
					pass
			if (not symbol) and th_txt == 'Traded as':
				try:
					symbol = re_obj.search(td_txt).group("symbol")
				except Exception:
					pass
			data[len(col_headers)+extra_col_headers.index("Symbol")] = symbol
			
		csv_writer.writerow([url]+data)

#get_company_attr('AMEX', amex_urls, 'wiki_company_data_AMEX.csv')
#get_company_attr('NYSE', nyse_urls, 'wiki_company_data_NYSE.csv')
#get_company_attr('NASDAQ', nasdaq_urls, 'wiki_company_data_NASDAQ.csv')
