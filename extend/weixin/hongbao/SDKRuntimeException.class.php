<?php
namespace weixin\hongbao;

/**
 * @author jy625
 */
class  SDKRuntimeException extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>