<?php
	class ajax_subscribe_php extends CPageCodeHandler 
	{
		public function ajax_subscribe_php()
		{
			$this->CPageCodeHandler();
		}
		public function PreRender()
		{
		}
		public function Render()
		{
			header('Content-type: text/html;charset=windows-1251');
			$email = trim(GP("email"));
			$cnt1 = SQLProvider::ExecuteScalar("select count(1) from vw__all_users where email = '$email'");
			$cnt2 = SQLProvider::ExecuteScalar("select count(1) from tbl__subscribed where email = '$email'");
			if ($cnt1+$cnt2 > 0) {
				print '1';
			}
			else {
				SQLProvider::ExecuteNonReturnQuery("insert into tbl__subscribed(email) values('$email')");
				print '0';
			}
		}
	}
?>
