<?php

	require_once("debug.php");
	//require_once("sqlcredentials.php");
	require_once("DatabaseTool.class.php");
	require_once("Suborganization.class.php");

	class OrganizationsTool
	{
		function GetAllOrganizationNames()
		{
			$retVal = array();
			
			//dprint("Trying to connect to database ...");
			
			//dprint("test: " . $test);
			//dprint("host: " . MYSQL_HOST);
			//dprint("user: " . MYSQL_USER);
			//dprint("pass: " . MYSQL_PASS);
			//dprint("db: " . MYSQL_DATABASE);
			
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get the total number of organizations from the data base
			$query = "SELECT name FROM organizations";
			$results = $dbtool->Query($query,$chandle);
			
			dprint("itterating through db response and creating return array");
			
			dprint("Count = " . mysql_num_rows($results));
			
			// create return array
			$retVal = array();
			
			// itterate through the results and populate an array
			while($r = mysql_fetch_assoc($results)) {
			
				// using the sub organizations id pull its name from the database
				$orgName = $r['name'];
			
				dprint("added " . $orgName);
			
				// add the new name to the list of names
				$retVal[] = $orgName;			
			}
			
			// return the created array
			return $retVal;
		}
	
		function GetAllSubOrganizationNames($orgname)
		{
			$retVal = array();
			
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get orgid from orgname
			$orgid = $this->OrgIdFromName($orgname);
			
			// select all suborganizations
			$query = "SELECT name FROM suborganizations WHERE organizationid =" . $orgid;
			$results = $dbtool->Query($query,$chandle);
			
			dprint("itterating through db response and creating return array");
			
			// itterate through the results and populate an array
			while($r = mysql_fetch_assoc($results)) {
			
				// using the sub organizations id pull it's name from the database
				$orgName = $r['name'];
			
				// add the new name to the list of names
				$retVal[] = $orgName;
			
			}
			
			return $retVal;
		}
	
		function GetAllSubOrganizations($orgname)
		{
			$retVal = array();
			
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get orgid from orgname
			$orgid = $this->OrgIdFromName($orgname);
			
			// select all suborganizations
			$query = "SELECT * FROM suborganizations WHERE organizationid =" . $orgid;
			$results = $dbtool->Query($query,$chandle);
			
			dprint("itterating through db response and creating return array");
			
			// itterate through the results and populate an array
			while($r = mysql_fetch_assoc($results)) {
			
				$suborg = new Suborganization();
			
				// populate our object with the database results
				$suborg->organiztionname = $this->OrgNameFromID($r['organizationid']);
				$suborg->name = $r['name'];
				$suborg->websiteurl = $r['websiteurl'];
				$suborg->documentsurl = $r['documentsurl'];
				$suborg->scriptname = $r['scriptname'];
				if( $r['dbpopulated'] == 1 )
				{
					$suborg->dbpopulated = "true";
				}
				else
				{
					$suborg->dbpopulated = "false";
				}
				$suborg->indexeddocs = $this->NumberOfIndexedDocsFromId($r['suborganizationid']);
				
				// add the new name to the list of names
				$retVal[] = $suborg;
			
			}
			
			return $retVal;
		}

		function NumberOfIndexedDocsFromId($id)
		{
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get sub org id from name
			$query = "SELECT count(*) FROM documents where suborganizationid='" . $id . "'";
			$result = $dbtool->Query($query,$chandle);
			
			// get row
			$r = mysql_fetch_assoc($result);
			
			return $r['count(*)'];
		}

		function SubOrgFromName($name)
		{
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get sub org id from name
			$query = "SELECT * FROM suborganizations where name='" . $name . "'";
			$result = $dbtool->Query($query,$chandle);
			
			// create an object to return
			$suborg = new Suborganization();
			
			// pull the id from the result
			//$r = mysql_fetch_row($result);
			
			// get result
			$r = mysql_fetch_assoc($result);
		
			dprint("Org ID: " . $r['organizationid']);
			dprint("Name: " . $r['name']);
			dprint("Website URL: " . $r['websiteurl']);
			dprint("Documents URL: " . $r['documentsurl']);
			dprint("Script Name: " . $r['scriptname']);
			dprint("DB Populated: " . $r['dbpopulated']);
			
			// populate our object with the database results
			$suborg->organiztionname = $this->OrgNameFromID($r['organizationid']);
			$suborg->name = $r['name'];
			$suborg->websiteurl = $r['websiteurl'];
			$suborg->documentsurl = $r['documentsurl'];
			$suborg->scriptname = $r['scriptname'];
			if( $r['dbpopulated'] == 1 )
				{
					$suborg->dbpopulated = "true";
				}
				else
				{
					$suborg->dbpopulated = "false";
				}
			$suborg->indexeddocs = $this->NumberOfIndexedDocsFromId($r['suborganizationid']);
			
			return $suborg;
		}
	
		function SubOrgIdFromName($name)
		{
			$retVal = -1;
			
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get sub org id from name
			$query = "SELECT suborganizationid FROM suborganizations where name='" . $name . "'";
			$result = $dbtool->Query($query,$chandle);
			
			// pull the id from the result
			$retVal = mysql_result($result,0);
			
			return $retVal;
		}
		
		function OrgIdFromName($name)
		{
			$retVal = -1;
			
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get the idea from the name via the database
			$query = "SELECT organizationid FROM organizations where name='" . $name . "'";
			$result = $dbtool->Query($query,$chandle);
			
			$r = mysql_fetch_row($result);
		
			$retVal = $r[0];
			
			return $retVal;
		}
	
		function SubOrgNameFromID($suborgid)
		{
		
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
		
			// get the name of the suborg from the id
			$query = "SELECT name FROM suborganizations where suborganizationid=" . $suborgid;
			$result = $dbtool->Query($query,$chandle);
			
			$r = mysql_fetch_row($result);
		
			$retVal = $r[0];
		
			// return the suborg name
			return $retVal;
		
		}
		
		function OrgNameFromID($orgid)
		{
		
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
		
			// get the name of the suborg from the id
			$query = "SELECT name FROM organizations where organizationid=" . $orgid;
			$result = $dbtool->Query($query,$chandle);
			
			$r = mysql_fetch_row($result);
		
			$retVal = $r[0];
		
			// return the suborg name
			return $retVal;
		
		}
		
		function OrgIdFromSubOrgId($suborgid)
		{
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get the name of the suborg from the id
			$query = "SELECT organizationid FROM suborganizations where suborganizationid=" . $suborgid;
			$result = $dbtool->Query($query,$chandle);
			
			// get first (only) result
			$r = mysql_fetch_row($result);
		
			// pull name from result
			$retVal = $r[0];
		
			// return the suborg name
			return $retVal;
		}
		
		function OrgNameFromSubOrgID($suborgid)
		{
			
			// get org id from suborg id
			$orgid = $this->OrgIdFromSubOrgId($suborgid);
			
			// get org name from org id
			$retVal = $this->OrgNameFromID($orgid);
			
			// return orgname
			return $retVal;
		}
		
		function IsIndexed($suborgname)
		{
			
			$retVal = false;
		
			// connect to DB
			$dbtool = new DatabaseTool();
			$chandle = $dbtool->Connect();
			
			// get the name of the suborg from the id
			$query = 'SELECT dbpopulated FROM suborganizations WHERE name="' . $suborgname . '"';
			$result = $dbtool->Query($query,$chandle);
			
			// get row
			$r = mysql_fetch_assoc($result);
		
			// pull out the dbpopulated field, as it represents if the suborg has been indexed fully
			if( $r['dbpopulated'] == '1' )
			{
				$retVal = true;
			}
			else
			{
				$retVal = false;
			}
			
			// return the suborg name
			return $retVal;
		}

	}

?>