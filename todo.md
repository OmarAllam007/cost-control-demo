# KPS Reports
## General
* Back button returns to budget


## Resource Dict
* Slow
* Filters
	* Status
	* Resource type, Resource Code/Name
	* BOQ Discipline (Filter only not in hierarchy)
	* Top resource or other
	* Cost/Var (plus or negative)
* Freeze table headers
* Export
* Hierarchy as of sent sheet


## Std Activity
* _Sum is not correct_
* _Hierarchy:_
	* _Activity Division_
	* _Std Activity_
* _Filters_
	* _Division_
	* _Activity_
	* _Negative var_
	* _Status_
* _Bordered tables_
* Charts


## Activity
* Same as Std Activity report but replace divisions by WBS


## Var Analysis
* Same hierarchy as resource dict. 
* Add previous price/unit
* If no current price/unit = 0
* to date var is for price/unit


## Boq
* Unit/price will not be summed
* Filters
	* WBS
	* Cost Account
	* Status


## Overdraft
* Physical revenue = physical unit (over all resources in BOQ) * BOQ unit rate
* Sum (budget cost over cost account)
* Sum (to date cost over cost account)
* Actual Qty -> Imported by cost account
* Physic. Unit Excl Price Var -> to date Qty * budget price/unit
* 
