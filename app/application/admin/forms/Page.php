<?php
class Form_Page 
{
	public function init()
	{
	
	}
	public function getPage($page = 1, $total,$url,$pagesize){
		if(empty($page)){$page=1;}
		$pageCount = ceil($total / $pagesize);
		$pageString = "<form  name='seachpage' id='seachpage' onsubmit='return checkNumber(".$pageCount.");' action='". $url . "' method='post'>";
		if ($pageCount <= 1) {
			$pageString .= '总页数:'.$pageCount.'&nbsp;&nbsp;<a>首页</a>|<a class="prev">上一页</a>&nbsp;<strong>1</strong>&nbsp;<a class="next">下一页</a>|<a>尾页</a>';
		} else {
			if ($page == 1) {
				$pageString .= '总页数:'.$pageCount.'&nbsp;&nbsp;<a>首页</a>|<a class="prev">上一页</a>';
			} else {
				$page_last = $page - 1;
				$pageString .= '总页数:'.$pageCount.'&nbsp;&nbsp;<a href=' . $url . '/page/1>首页</a>|<a href=' . $url . '/page/' . $page_last . ' class="prev">上一页</a>';
			}
			if ($pageCount <= 7) {
				for ($i = 1; $i <= $pageCount; $i ++) {
					if($i==$page){
						$pageString .= '<strong>' . $i . '</strong>';
					}else{
						$pageString .= '<a href=' . $url . '/page/' . $i . '>' . $i . '</a>&nbsp;';
					}
				}
			} else {
				$m = ($page - 4) >= 1 ? $page - 3 : 1;
				if($page == $pageCount){
					$m = $pageCount-6;
				}
				$n = $m+6;
				$n=$n>$pageCount?$pageCount:$n;
				for ($i = $m; $i <=$n; $i ++) {
					if($i==$page){
						$pageString .= '<strong>' . $i . '</strong>';
					}else{
						$pageString .= '<a href=' . $url . '/page/' . $i . '>' . $i . '</a>&nbsp;';
					}
				}
			}
			if (($page == $pageCount) || ($pageCount == 0)){
				$pageString.='<a class="next">下一页</a>|<a>尾页</a>&nbsp;';
			} else {
				$page_next = $page + 1;
				$pageString .= '<a href=' . $url . '/page/' . $page_next . ' class="next">下一页</a>|<a href=' . $url . '/page/' . $pageCount . '>尾页</a>&nbsp;';
			}
		}
		return $pageString;
	}
}