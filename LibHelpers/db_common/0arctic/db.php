<?php   // © Copyright 2017, Michael Scarborough.  All rights reserved.



class Db
{

	private $useRDBMS = false;
	private $strDefaultDest = NULL;
	private $datastoreName;


	$arrDefaultMemDB = array();  // simple key-value datastore, default if no RDBMS linked
	//TO-DO: add persistence to storage of array data (save to disk file, probably)



	/**
	* constructor for Db class
	*
	* @param (string) $datastoreNameP   name of the database or the flat disk file to be linked to this object
	* @param (bool) $useRDBMSp flag determining if this data access class will be linked to an RDBMS
	* @return (NULL) no return value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function __construct($datastoreNameP, $useRDBMSp)
	{
		$useRDBMS = $useRDBMSp;

		//for demonstration script, force only simple datastore (no RDBMS)
		if ($useRDBMSp !== false)
		{
				DBLog::msg("Error creating data access object.  Only simple datastore allowed at this time.");
				exit(1);
		}

	}



	/**
	* receive key-value pair and store it using the default simple datastore or an RDBMS
	*
	* @param (string) $strData  this is the value to store
	* @param (string) $strKey  this is the key to store, if NULL then it will be assigned an auto-increment ID
	* @param (string) $strDest  an RDBMS table name
	* @param (string) $strConditions  the where clause for RDBMS access
	* @return (string)  returns the key used to store the value
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function storeData($strData, $strKey, $strDest=NULL, $strConditions=NULL)
	{
		$autoinc = false;

		if ($strDest == NULL)
		{
			if ($useRDBMS !== false)
			{
				//error
				DBLog::msg("Error storing data.  No Destination supplied.");

			}
			 else
			{
				//save key-value pair to our simple datastore, the default array
				if ($strKey == NULL)
				{
					$arrDefaultMemDB[] = $strData;
					end($arrDefaultMemDB);
					$autoinc = key($arrDefaultMemDB);
				}
				 else
				{
					$arrDefaultMemDB[$strKey] = $strData;
					$autoinc = key($arrDefaultMemDB);
					persistSimpleData();

				}

			}



		}
		 else
		{
			//TO-DO: implement RDBMS wrapper function for INSERTs
		}



		return $autoinc;
	}




	/**
	* receive key ID and retrieve the corresponding value either using the default simple datastore or an RDBMS
	*
	* @param (string) $strKey  this is the key to find
	* @param (string) $sortkey  sub-key to use if sorting is needed
	* @param (bool) $sortAscending  true for ascending or false for a descending sort order
	* @param (string) $strSource  an RDBMS table name
	* @param (string) $strConditions  the where clause for RDBMS access
	* @return (bool)  returns true on success, and false on failure
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function getData($strKey, $sortkey=NULL, $sortAscending=NULL, $strSource=NULL, $strConditions=NULL)
	{
		$ret = NULL;

		if ($strSource == NULL)
		{
			if ($useRDBMS !== false)
			{
				//error
				DBLog::msg("Error getting data.  No Source supplied.");

			}
			 else
			{
				//retrieve key-value pair from our simple datastore, the default array

				$ret = $arrDefaultMemDB[$strKey];
			}


		}
		 else
		{
			//TO-DO: implement RDBMS wrapper function for SELECTs

		}


		return $ret;

	}





	/**
	* receive key ID and destroy the corresponding key-value pair either using the default simple datastore or an RDBMS
	*
	* @param (string) $strKey  this is the key to find
	* @param (string) $sortkey  sub-key to use if sorting is needed
	* @param (bool) $sortAscending  true for ascending or false for a descending sort order
	* @param (string) $strSource  an RDBMS table name
	* @param (string) $strConditions  the where clause for RDBMS access
	* @return (bool)  returns true on success, and false on failure
	* @author     Mike Scarborough
	* @version    1.0
	*/
	public function removeData($strKey, $sortkey=NULL, $sortAscending=NULL, $strSource=NULL, $strConditions=NULL)
	{
		$ret = false;

		if ($strSource == NULL)
		{
			if ($useRDBMS !== false)
			{
				//error
				DBLog::msg("Error removing data.  No Source supplied.");

			}
			 else
			{
				//retrieve key-value pair from our simple datastore, the default array

				if (isset($arrDefaultMemDB[$strKey]))
				{
					unset($arrDefaultMemDB[$strKey]);
					$ret = true;
					persistSimpleData();
				}
			}


		}
		 else
		{
			//TO-DO: implement RDBMS wrapper function for DELETEs

		}


		return $ret;

	}






	/**
	* write the default array out to disk
	*
	* @return (bool) true on success, false on failure
	* @author     Mike Scarborough
	* @version    1.0
	*/
	private function persistSimpleData()
	{

		//TO-DO:  implement disk storage to unique file

		return false;

	}


}




?>