<?php
include("config\config.php");


function calculate($symbol,$link){
	
	#Get the required data for calculation
		/*
			TODO:
			List all the equations
				Book Value% = 
				Revenue Growth >15% = 
				Gross Profit Growth >15% = 
				gross margin>40% = Gross Profit / Total Revenue(季/年)
				S-Expense/Gross Profit <30%~80% = (Selling/Administrative Expenses) / Gross Profit
				R&D-Exp/Gross Profit low = Research & Development / Gross Profit
				Depre./Gross Profit <15% = (Depreciation/Amortization) / Gross Profit
				Interset/Gross.<15%(consumer product) = Interest Expense / Gross Profit
				Operating Income Growth% = 
				Income After Tax Growth% = 
				Income/Gross Profit>10~20% = Income After Tax(季/年) / Total Revenue(季/年)
				EPS Growth% = 
				Receivables/Sales low = Total Receivables, Net / Total Revenue(季/年)
				Cost/Inventory High = Cost of Revenue / Total Inventory
				Equipment/Asset low = Total Receivables, Net / Total Revenue(季/年)
				Debt Redeem yr < 4 yr = Long Term Debt(季/年) / Income After Tax(季/年)
				Total Equity = Total Assets(季/年) - Total Liabilities(季/年)
				Debt/Equity <0.8 = (Total Liabilities(季/年)) / (Total Equity - Treasury Stocks)
				Retained Earnings Growth >5% = 
				C-E/ Net Income<25%~50% = Capital Expenditures/Income After Tax(季/年)
				PE = Avg10(Avg P/E)
			List all the required fields
			Plan the queries
			Come up with a simple data structure that is suitable for calculation
		*/
	
	#Calculate
		/*
			TODO:
			Do the calculations based on the above equations
			Come up with a simple data structure that is suitable for result insertion
		*/
	
	#store the calculated data into the database
	
	
}

function get_growth_rate_ann($SYMBOL, $NumOfYears){
	//Get current year
	$Today = getdate(); 
	$ThisYear = $today['year'];
	
	//Get recent $NumOfYears years data from DB
	connect_db();
	for ($i = 1; $i <= $NumOfYears; $i++){
		$Year = $ThisYear - $i;
		
	}
}
?>