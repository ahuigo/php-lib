<?php
    function statistic($where, $bind){
        if(isset($_GET['statistic'])){
            if(isset($_GET['date_format']) and preg_match('#^[%\w]+$#', $_GET['date_format'])) {
                $date_format = $_GET['date_format'];
            }else{
                $date_format = '%Y%m';
            }
            $conf = array(
                'source_id' => array(
                    'group'=> 'source_id',
                ),
                'pay_time' => array(
                    'group'=> "date_format(pay_time,'{$date_format}')",
                ),
            );
            $select = "select sum(amount) as cost";
            $groups = array();
            if(!empty($_GET['groupBy'])){
                foreach(explode(',', $_GET['groupBy']) as $field){
                    if(isset($conf[$field])){
                        $col = $conf[$field]['group'];
                        $select .= ",{$conf[$field]['group']}";
                        $groups[] = "{$col}";
                    }
                }
            }
            $sql = "$select from pay_log $where ";
            if($groups){
                $sql = "$sql group by ".implode(',' ,$groups);
            }
            $list = $this->db->run($sql, $bind);
            if($list){
               array_unshift($list, array_keys($list[0]));
				foreach($list as $bill){
					foreach($bill as $k => $v){
					   echo "$v\t";
					}
					echo "\n";
				}
            }
            die;
        }
    }
