<html>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<head><link href="base.css" rel="stylesheet" type="text/css"></head>
    <body>
        <?php
			
            include("sessionCheck.php");
            include("PHPconnectionDB.php");
            if (!sessionCheck()) {
                echo 'Not logged in! <br/>';
            }
            else {
            	error_reporting(E_ALL ^ E_NOTICE);
            	
            	$conn = connect();
            	
            	// Add
            	if($_POST["hdnCmd"] == "Add")
            	{
            		$str = "insert into radiology_record ";
            		$str .="(record_id, image_id, thumbnail, regular_size, full_size) ";
            		$str .="values ";
            		$str .="('".$_POST["txtAddrecord_id"]."','".$_POST["txtAddimage_id"]."' ";
            		$str .=",'".$_POST["txtAddthumbnail"]."' ";
            		$str .=",'".$_POST["txtAddregular_size"]."' ";
            		$str .=",'".$_POST["txtAddfull_size"]."') ";
            		
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Add [".$e['message']."]";
            		}

            	}
				
            	// Update
            	if($_POST["hdnCmd"] == "Update")
            	{

            		$str = "update radiology_record set ";
            		$str .="record_id = '".$_POST["txtEditrecord_id"]."' ";
            		$str .=",image_id = '".$_POST["txtEditimage_id"]."' ";
            		$str .=",thumbnail = '".$_POST["txtEditthumbnail"]."' ";
            		$str .=",regular_size = '".$_POST["txtEditregular_size"]."' ";
            		$str .=",full_size = '".$_POST["txtEditfull_size"]."' ";
            		$str .="where record_id = '".$_POST["hdnEditrecord_id"]."' ";
            		$stid = oci_parse($conn, $str);     
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Update [".$e['message']."]";
            		}

            	}
            	
            	// Delete
            	if($_GET["Action"] == "Del")
            	{
            		$str = "delete from radiology_record where record_id = '".$_GET["keyID"]."' ";
            		$stid = oci_parse($conn, $str);
            		$res = oci_execute($stid);
            		if($res)
            		{
            			oci_commit($conn);
            		}
            		else
            		{
            			$e = oci_error($stid);
            			echo "Error Delete [".$e['message']."]";
            		}
            	} 	
                
				$sql="select * from radiology_record";
                $stid = oci_parse($conn, $sql);
                $res = oci_execute($stid);
                if (!$res) {
                    $err = oci_error($stid);
                    echo htmlentities($err['message']);
                }
                else {
                ?>    
               
					<h2>Radiology Record Module</h2>
					
					<div class="tabGroup">
				    <input type="radio" name="tabGroup1" id="rad1" class="tab1" onclick="document.location.href='manageUsers.php'" />
				    <label for="rad1">Radiology Record</label>
				 
				    <input type="radio" name="tabGroup1" id="rad2" class="tab2" checked="checked"/>
				    <label for="rad2">Upload Images</label>
				     

				     
				    <br/>
				 
				    <div class="tab1"></div>
				    <div class="tab2">
					
                	<form name="frmMain" method="post" action="<?=$_SERVER["PHP_SELF"];?>">
                	<input type="hidden" name="hdnCmd" value="">
                	<table  >
                						
					<tr>
					    <th width=25> <div align="center">Record ID</div></th>
					    <th> <div align="center">Image ID </div></th>
					    <th width=25 > <div align="center">Thumbnail </div></th>
					    <th> <div align="center">Regular Size </div></th>
					    <th> <div align="center">Full Size </div></th>
					</tr>
  					<?php 
				//while ($row=oci_fetch_array($stid, OCI_BOTH))
				while ($row=oci_fetch_array($stid, OCI_BOTH))

					{

						if( $row["record_id"] == $_GET["keyID"] and $_GET["Action"] == "Edit")
						{
						?>
						<tr>
							<td><div align="center">
								<input type="text" name="txtEditrecord_id"  size="1" value="<?=$row['record_id'];?>">
								<input type="hidden" name="hdnEditrecord_id" value="<?=$row["record_id"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditimage_id" size="15" value="<?=$row["image_id"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditthumbnail" size="15" value="<?=$row["thumbnail"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditregular_size" size="15" value="<?=$row["regular_size"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditfull_size" size="15" value="<?=$row["full_size"];?>">
							</div></td>
							<td><div align="center">
								<input type="text" name="txtEditphone" size="15" value="<?=$row["PHONE"];?>">
							</div></td>	


							<td colspan="2" align="right"><div align="center">
								<input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="frmMain.hdnCmd.value='Update';frmMain.submit();">
								<input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?=$_SERVER["PHP_SELF"];?>';">
							</div></td>
						</tr>
						<?php
						}
						else
						{
  						?>
						  
						    <tr>
							    <td><div align="center"><?=$row["record_id"];?></div></td>
							    <td><div align="center"><?=$row["image_id"];?></div></td>
							    <!--
							    <td><div align="center"><?=$row["thumbnail"];?></div></td>
							    <td><div align="center"><?=$row["regular_size"];?></div></td>
							    <td><div align="center"><?=$row["full_size"];?></div></td>
							    -->
							    <td align="center"><a href="<?=$_SERVER["PHP_SELF"];?>?Action=Edit&keyID=<?=$row["record_id"];?>"><img src="edit-16x16.png"></a></td>
								<td align="center"><a href="JavaScript:if(confirm('Confirm Delete?')==true){window.location='<?=$_SERVER["PHP_SELF"];?>?Action=Del&keyID=<?=$row["record_id"];?>';}"><img src="delete-16x16.png"></a></td>
					  		</tr>
						  
					   	<?php
						}
					}
				  	?>

				<tr>
				    <td><div align="center"><input type="text" name="txtAddrecord_id" size="15" ></div></td>
				    <td><div align="center"><input type="text" name="txtAddimage_id" size="15"></div></td>
				    <!-- 
				    <td><div align="center"><input type="text" name="txtAddthumbnail"  size="15"></div></td>
					<td><div align="center"><input type="text" name="txtAddregular_size"  size="15"></div></td>
				    <td><div align="center"><input type="text" name="txtAddfull_size"  size="15"></div></td>				  				    
				    <td colspan="2" align="right"><div align="center">
				      <input name="btnAdd" type="image" src="plus-16x16.png" id="btnAdd" value="Add" OnClick="frmMain.hdnCmd.value='Add';frmMain.submit();">
				    </div></td>
			    </tr>
			</table>
		</form>
		<?php
		}
		?>
		</div>


	<?php 
    }
	?>					
    </body>
</html>
