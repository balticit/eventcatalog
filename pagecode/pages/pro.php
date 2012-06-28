<?php
	class pro_php extends CPageCodeHandler 
	{
		public function pro_php()
		{
			$this->CPageCodeHandler();
		}
		
		public function PreRender()
		{
			$av_rwParams = array("type");
		  CURLHandler::CheckRewriteParams($av_rwParams);
			$type = GP("type");
			switch($type){
				case "result":
					$cost = GetParam("OutSum","p");
					$invId = GetParam("InvId","p");
					$signValue = GetParam("SignatureValue","p");
					if (is_numeric($cost) && is_numeric($invId) &&
							strtoupper($signValue) == strtoupper(md5("$cost:$invId:".ROBOX_PASS2))){
						$res = SQLProvider::ExecuteQuery("select cost from tbl__pro_accounts where tbl_obj_id = $invId and payed=0");
						if (is_array($res) && sizeof($res)){
						  $my_cost = $res[0]['cost'];
							if ($my_cost == $cost) {
								SQLProvider::ExecuteNonReturnQuery("
									update tbl__pro_accounts set payed = 1 where tbl_obj_id = $invId and payed=0");
								echo "OK$invId\n";
								exit();
							}
							else{
								echo "bad OutSum\n";
								exit();
							}
							
						}
						else{
							echo "bad ID\n";
							exit();
						}
						
					}
					else {
						echo "bad sign\n";
						exit();
					}
					break;
				case "success":
					CURLHandler::Redirect("/r_cabinet/?data=pro");
					break;
				case "fail":
					$cost = GetParam("OutSum","p");
					$invId = GetParam("InvId","p");
					$signValue = GetParam("SignatureValue","p");
					if (is_numeric($cost) && is_numeric($invId) &&
							strtoupper($signValue) == strtoupper(md5("$cost:$invId:".ROBOX_PASS1))){
						$res = SQLProvider::ExecuteQuery("select cost from tbl__pro_accounts where tbl_obj_id = $invId and payed=0");
						if (is_array($res) && sizeof($res)){
						  $my_cost = $res[0]['cost'];
							if ($my_cost == $cost) {
								SQLProvider::ExecuteNonReturnQuery("
									update tbl__pro_accounts set payed = -1 where tbl_obj_id = $invId and payed=0");
							}
						}
					}
					CURLHandler::Redirect("/r_cabinet/?data=pro");
					break;
				default:
				  echo "error type\n";
					exit();
			}
		}
	} 
?>