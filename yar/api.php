<?php
class API {
    /**
     * the doc info will be generated automatically into service info page.
     * @param
     * @return
     */
    public function test($param, $option = "foo") {
		return [$param, $option];
    }
 
    protected function client_can_not_see() {
    }
}
 
$service = new Yar_Server(new API());
`date >> a.txt`;
$service->handle();
