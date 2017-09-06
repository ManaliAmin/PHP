<!DOCTYPE html>
<html>
<!--
group members:

M.Saeed Mohiti		116236159

Adriel Arce De La Cruz		106336167

Saif Husain Khan	125444158

Manali Amin		119679157

Digvijaysingh Makwana	101918167

-->

<head>
	<title>Assignment 1</title>
</head>
<body>
<?php 

	//retrieve server, user, and database info from topsecret.txt.
	$lines = file('topsecret.txt');
	$servername = trim($lines[0]);
	$dbusername = trim($lines[1]);
	$dbpassword = trim($lines[2]);
	$dbname = trim($lines[3]);
	
	//create array from the txt files
	$model = file('model.txt');
	$os = file('os.txt');
	$version = file('version.txt');
	$price = file('price.txt');
	//trim the strings stored in the arrays
	for ($i = 0; $i < sizeof($model); $i++){
		$model[$i] = trim($model[$i]);
		$os[$i] = trim($os[$i]);
		$version[$i] = trim($version[$i]);
		$price[$i] = trim($price[$i]);
	}
	//connect to mysqli database
	$conn = new mysqli($servername,$dbusername,$dbpassword,$dbname);
	if($conn->connect_error){
		die("Connection Failed: " . $conn->connect_error);
	}
	//delete from table to make sure it is empty
	$query = "DELETE FROM cellphones;";
	mysqli_query($conn, $query);
	//inserting values from files into CELLPHONES table
	for ($i=0; $i < sizeof($model); $i++){
		$sql = "INSERT INTO cellphones(model, os_name, version, price)
		Values ('$model[$i]', '$os[$i]', '$version[$i]', '$price[$i]')";
		$result = mysqli_query($conn, $sql);		
	};
	//variables for storing errors
	$osErr = "";
	$minErr = "";
	$maxErr = "";
	//input box validation
	$osValid = false;
	$minValid = false;
	$maxValid = false;
	//whole form valid
	$formValid = false;
	//storing values from form into variables
	$selectOS = $_POST['os'];
	$selectMIN = $_POST['minPrice'];	
	$selectMAX = $_POST['maxPrice'];


	if ($_POST){
		//OS field valid if any is selected.
		if ($selectOS == "none"){
			$osErr = "You must select an operating system";
		}
		else{
			$osValid = true;
		}
		//minPrice field valid if minimum price > 0 and not empty.
		if ($selectMIN < 0){
			$minErr = "Price cannot be less than $0";
		}
		else if ($selectMIN == ""){
			$minErr = "You must enter a MINIMUM price";
		}
		else{
			$minValid = true;
		}
		//maxPrice field valid if not empty and > minPrice.
		if ($selectMAX == ""){
			$maxErr = "You must enter a MAXIMUM price";
		}
		else if ($selectMAX < 0){
			$maxErr = "Price cannot be less than $0";
		}
		else if ($selectMAX < $selectMIN){
			$maxErr = "Maximum price cannot be less than the minimum";
		}
		else{
			$maxValid = true;
		}
		//if the fields are valid, then whole form is valid
		if ($osValid && $minValid && $maxValid){
			$formValid = true;
		}
	}
		
?>
	<!--CREATE FORM-->
	<form method="POST" action="">
  	<font color="blue">Select a CellPhone OS:</font>
  	</br></br>
  	<select name="os"><br/>
		<option value="none">-Select OS-</option>
  		<option value="Android" <?php if($_POST['os'] == 'Android') echo "selected"; ?>>Android</option>
  		<option value="iOS" <?php if($_POST['os'] == 'iOS') echo "selected"; ?> >iOS</option>
  		<option value="BlackBerry" <?php if($_POST['os'] == 'BlackBerry') echo "selected"; ?>>BlackBerry</option>
  		<option value="Windows" <?php if($_POST['os'] == 'Windows') echo "selected"; ?> >Windows</option>
  	</select>
  	<font color="red"><?php if ($osValid == false)echo $osErr; ?></font>
  	</br>
  
  	</br></br>
  	<font color="blue">Price Range:</font>
  	</br></br>
  	<font color="blue">Min</font>
  	<input type="number" name="minPrice" value= "<?php if (isset($_POST['minPrice'])) echo $_POST['minPrice'];?>">
  	<font color="red"><?php if ($minValid == false)echo $minErr; ?></font>
  	</br>
  	<font color="blue">Max</font>
  	<input type="number" name="maxPrice" value="<?php if (isset($_POST['maxPrice'])) echo $_POST['maxPrice'];?>">
  	<font color="red"><?php if ($maxValid == false)echo $maxErr; ?></font>
  	</br>

  	<input type="submit" value="Search">
  	</form> 


<?php
	//if form was submitted and all fields are valid
	if ($formValid && $_POST){
		//connect to database
		$conn = new mysqli($servername,$dbusername,$dbpassword,$dbname);
		if($conn->connect_error){
			die("Connection Failed: " . $conn->connect_error);
		}
		//create sql 'select' query
		$sqlSelect = "SELECT * FROM cellphones WHERE os_name = '$selectOS' AND price BETWEEN $selectMIN AND $selectMAX ORDER BY price desc;";
		$result = mysqli_query($conn, $sqlSelect) or die ('</br>query failed </br>' . mysqli_error($conn));
		echo "</br>";
		//print the date on which the query was created
		$timeQuery = "SELECT CURDATE()";
		$date = mysqli_query($conn, $timeQuery) or die ('ERROR DATE QUERY'.mysqli_error($conn));
		$printDate = mysqli_fetch_assoc($date);
		echo "Query Date: ". $printDate['CURDATE()'];
		echo "</br></br>";

		//print the table to display query results
		echo "<table border='2px solid black'>";
		echo "<caption><font color='green'>Search Results: </font></caption>";
    	echo "<th>Model</th><th>OS</th><th>Version</th><th>Price</th>";
    	while ($row = mysqli_fetch_assoc($result)){
       		echo "<tr>";
       		echo "<td>" . $row['model'] . "</td>";
       		echo "<td>" . $row['os_name'] . "</td>"; 
       		echo "<td>" . $row['version'] . "</td>"; 
       		echo "<td>" . $row['price'] . "</td>"; 
       		echo "</tr>";
   		}
		echo "</table>";	
		echo "</br></br>";
		//close connection
		mysql_close();
	} 
?>
</body>
</html>