<?php
/*
 * Uվ�ɼ��࣬ҳ��ƥ����Ʒiid����Ʒ������,�����ֵͨ��API��ȡ 
*/
class UzCaiji{
	public $url;
	public $items;

	public function __construct(){
		$this->items = array();
	}
	/*
	 *  �ɼ����� 
	 *  website:�ɼ���ַ��������
	 *  page:Ĭ�ϲɼ�һҳ,Ŀǰ���Ի�ȡ��ҳ��վ�� jiukuaiyou��juanpi,zhuanbao
	 *  mode:����ģʽ,Ĭ��Ϊֱ�ӵ������ݿ�,ֵ 2 Ϊ���json��ʽ,ֵ3Ϊ�����Ʒҳ��
	 */ 
	public function Caiji($website,$page=1,$mode=1){
                
                if(file_exists('./include/eachptns/'.$website.'.php')){
                    require 'eachptns/'.$website.'.php';
                    global $contentptn,$singleptn;    
                }
                
//                if(!$GLOBALS['G_SP']['autocat']){
//                    require 'eachcatsurl/'.$website.'.php';
//                    global $catItemsUrl;
//                }
                
		if($website){
			if($website=='huiyuangou'){ // ��Ա��
//                            echo get_redirect_url_pro("http://s.click.taobao.com/MUbiFex","http://ai.taobao.com");
                                $huiyuangou = null;
                                for($j=1;$j<=10;$j++){
                                    $this->url = 'http://appapi.huipinzhe.com/mobapi/product/list?mod=listnew&page='.$j;
                                    $result = file_get_contents($this->url);
                                    $res = json_decode($result,TRUE);                                  
                                    foreach($res['goodsArray'] as $k => $v){
                                        $itemRes = parse_url(get_redirect_url_pro($v['detailUrl'],"http://ai.taobao.com"));
                                        $itemRes = convertUrlQuery($itemRes['query']);
                                        $djzk[] = array('iid'=>$itemRes['id'],'nprice'=>$v['cPrice'],'pic'=>$v['picUrl']);
                                        $itemRes = null;
                                        $matches = null;
                                    }
                                    $huiyuangou[$j] = $djzk;
                                    $djzk = null;
                                    $result = null;
                                    $res = null;
                                }
				
//				var_dump($huiyuangou);
				$this->items = $huiyuangou;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='jiukuaiyou'){ // �ſ���

				if($mode==3){ // ����Ҫ�ɼ���ҳ��,�ſ�����ƷΪ45��һҳ
					$this->url = 'http://jiukuaiyoucom.uz.taobao.com/';
					$result = get_contents($this->url);
				                                      
					$ptn = '/<div class="head"(.+?)class="main"/is';
					preg_match_all($ptn,$result,$jiuarr,PREG_SET_ORDER);
					//print_r($jiuarr);
					
					$ptn = '/<div class="nav-show(.+?)������Ʒ(.+?)<\/li>/is';
					preg_match_all($ptn,$jiuarr[0][0],$jiuarr1,PREG_SET_ORDER);
					//print_r($jiuarr1);
					
					$ptn = '/(\d+)/is';
					preg_match_all($ptn,$jiuarr1[0][2],$jiuarr,PREG_SET_ORDER);
					if($jiuarr[0][1]){
						return $jiuarr[0][1];
					}else{
						return false;
					}
				}else{
                                        if(true) //�Զ�����
                                            $catItemsUrl = 'http://api.juanpi.com/open/jiukuaiyou';
                                        
                                        if(is_array($catItemsUrl)){// ���Զ�����
                                            
                                            foreach($catItemsUrl as $cat=>$url){
                                                
                                                $result = get_contents($url);
                                                
                                                $content = getcaijicontent($result,$contentptn,$singleptn);
                                                  
                                                foreach($content as $k => $v){
                                                    $jiuarr2[] = array('iid'=>$v[7],'nprice'=>$v[3],'cat'=>$cat);
                                                }
                                                
                                                
                                                $jiukuaiyou['cat'.$cat] = $jiuarr2;
                                                $jiuarr2 = null;
                                                $result = null;
                                                $content = null;
                                            }
                                        }else{
                                            
                                            $result = file_get_contents($catItemsUrl);	
                                            $res = preg_replace('/jsonpReturn/i','',$result);
                                            $res = ltrim($res,'(');
                                            $res = rtrim($res,');');
                                            $res = json_decode($res,TRUE);
                                            foreach($res['goodslist'] as $k => $v){
                                               $jiuarr2[] = array('iid'=>preg_replace('/(.+?)id=/i','$3',$v['deal_taobao_link']),'nprice'=>preg_replace('/[^0-9][^0-9]/i','',$v['deal_price']),'pic'=>$v['deal_image']);
                                            }
                                            $jiukuaiyou['all'] = $jiuarr2;
                                        }
					
//                                        var_dump($jiukuaiyou);
					$this->items = $jiukuaiyou;
					
					if($mode==2)
						echo json_encode($this->items);
				}
			}elseif($website=='mytehui'){ // VIP�ػ�
				$this->url = 'http://mytehui.uz.taobao.com/';
				$result = get_contents($this->url);
				// ƥ�䱬��
				$thptn = '/id="main"(.+?)class="tmcon"(.+?)class="ygtm"/is';
				preg_match_all($thptn,$result,$bkarr,PREG_SET_ORDER);
				$thptn = '/class="buylink"(.+?)<a(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="buybtn"(.+?)class="cRed"(.+?)(\d+\.?\d+)(.+?)<\/strong>/is';
				preg_match_all($thptn,$bkarr[0][2],$bkarr1,PREG_SET_ORDER);
				//print_r($bkarr1);
				foreach($bkarr1 as $k => $v){
					$bk[] = array('iid'=>$v[4],'nprice'=>$v[9]);
				}
				$mytehui['bk'] = $bk;
				// end - ƥ�䱬��
				//var_dump($mytehui);
				
				// ƥ��һ��������
				$yfzptn = '/class="mainbox"(.+?)class="msblist(.+?)<\/ul>/is';
				preg_match_all($yfzptn,$result,$yfzarr,PREG_SET_ORDER);
				//echo $yfzarr[0][0];
				$yfzptn = '/<li>(.+?)class="msbinfo"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="vip_price"(.+?)(\d+\.?\d+)(.+?)<\/li>/is';
				preg_match_all($yfzptn,$yfzarr[0][0],$yfzarr1,PREG_SET_ORDER);
				//print_r($yfzarr1);
				foreach($yfzarr1 as $k => $v){
					$yfz[] = array('iid'=>$v[4],'nprice'=>$v[8]);
				} 
				$mytehui['yfz'] = $yfz;
				// ƥ��һ������������
				//var_dump($mytehui);
				
				// ƥ��9.9����
				$tjbyptn = '/class="mainbox(.+?)class="ninebox"(.+?)class="nextnine"/is';
				preg_match_all($tjbyptn,$result,$tjbyarr,PREG_SET_ORDER);
				$tjbyptn = '/<li>(.+?)class="ninfo"(.+?)class="vip_price"(.+?)(\d+\.?\d+)(.+?)class="vip_buy"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="vipbuybtn"(.+?)<\/li>/is';
				preg_match_all($tjbyptn,$tjbyarr[0][2],$tjbyarr1,PREG_SET_ORDER);
				foreach($tjbyarr1 as $k => $v){
					$tjby[] = array('iid'=>$v[8],'nprice'=>$v[4]);
				}
				$mytehui['tjby'] = $tjby;
				// ƥ��9.9���ʽ���
				//var_dump($mytehui);
				
				// ƥ����������
				$rxmcptn = '/class="mainbox"(.+?)class="msslist(.+?)<\/ul>/is';
				preg_match_all($rxmcptn,$result,$rxmcarr,PREG_SET_ORDER);
				$rxmcptn = '/<li>(.+?)class="msbinfo"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/h3>(.+?)class="vip_price"(.+?)(\d+\.?\d+)<\/span>(.+?)<\/li>/is';
				preg_match_all($rxmcptn,$rxmcarr[0][2],$rxmcarr1,PREG_SET_ORDER);
				foreach($rxmcarr1 as $k => $v){
					$rxmcarr2[] = array('iid'=>$v[4],'nprice'=>$v[9]);
				}
				$mytehui['rxmc'] = $rxmcarr2; 
				//print_r($rxmcarr1);
				// ƥ��������������
				
				//var_dump($mytehui);
				$this->items = $mytehui;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='vipgouyouhui'){ // VIP���Ż�
				$this->url = 'http://vipgouyouhui.uz.taobao.com/';
				$result = get_contents($this->url);
				$gyhptn = '/class="Container"(.+?)class="indexcontent"(.+?)class="banner"(.+?)class="banner_list"(.+?)<\/div>(.+?)VIPר��(.+?)>(\d+\.?\d+)<\/span>(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)style="clear:both;"/is';
				preg_match_all($gyhptn,$result,$gyharr,PREG_SET_ORDER);
				foreach($gyharr as $k => $v){
					$bk[] = array('iid'=>$v[10],'nprice'=>$v[7]);//����
				}
				$vipgouyouhui['bk'] = $bk;
				
				$gyharr = null;
				// �Լ��ٹ�
				$xjsgptn = '/class="shop"(.+?)class="ad"/is';
				preg_match_all($xjsgptn,$result,$xjsgarr,PREG_SET_ORDER);
				$xjsgResult = $xjsgarr[0][0];
				$xsqgResult = $xjsgarr[1][0];// ��ʱ����
//				echo $xsqgResult;
				$xjsgptn = '/<li(.+?)class="shoptitle"(.+?)class="newcxj">(.+?)>(\d+\.?\d+)<\/span>(.+?)style="float:right;"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/li>/is';
				preg_match_all($xjsgptn,$xjsgResult,$xjsgarr1,PREG_SET_ORDER);
//				print_r($xjsgarr1);
				foreach($xjsgarr1 as $k => $v){
					$xjsg[] = array('iid'=>$v[8],'nprice'=>$v[4]);//,'pic'=>$v[8]
				}
				$vipgouyouhui['xjsg'] = $xjsg;
				// end - �Լ��ٹ�
				
				// ��ʱ����
				$xsqgptn = '/<li(.+?)class="shoptitle"(.+?)class="newcxj">(.+?)>(\d+\.?\d+)<\/span>(.+?)style="float:right;"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/li>/is';
				preg_match_all($xsqgptn,$xsqgResult,$xsqgarr,PREG_SET_ORDER);
//				print_r($xsqgarr);
				foreach($xsqgarr as $k => $v){
					$xsqg[] = array('iid'=>$v[8],'nprice'=>$v[4]);//,'pic'=>$v[8]
				}
				$vipgouyouhui['xsqg'] = $xsqg;
				// end- ��ʱ�ٹ�
				
				// ׬��
//				$gyhptn = '/class="imgs"(.+?)<a(.+?)href="(.+?)[?,]id=(\d+)(.+?)"(.+?)<img(.+?)src="(.+?)"(.+?)��(.+?)<b>(\d+\.?\d+)<\/b>/is';
//				preg_match_all($gyhptn,$zbR,$gyharr,PREG_SET_ORDER);
//				//print_r($gyharr);
//				foreach($gyharr as $k => $v){
//					$zb[] = array('iid'=>$v[4],'nprice'=>$v[11]);//,'pic'=>$v[8]
//				}
//				$vipgouyouhui['zb'] = $zb;
				// END - ׬��
				
				
				// ��������
				$bkrmptn = '/class="shop2"(.+?)class="ad"/is';
				preg_match_all($bkrmptn,$result,$bkrmarr,PREG_SET_ORDER);
				$bkrmResult = $bkrmarr[0][0];
				//print_r($bkrmResult);
//				echo $bkrmResult;
				$bkrmptn = '/<li(.+?)class="newcxj">(.+?)>(\d+\.?\d+)<\/span>(.+?)style="float:right;(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)style="clear:both;"(.+?)<\/li>/is';
				preg_match_all($bkrmptn,$bkrmResult,$bkrmarr1,PREG_SET_ORDER);
//				print_r($bkrmarr1);
				foreach($bkrmarr1 as $k => $v){
					$bkrm[] = array('iid'=>$v[7],'nprice'=>$v[3]);//,'pic'=>$v[3]
				}
				$vipgouyouhui['bkrm'] = $bkrm;
				// end - ��������
			
//				var_dump($vipgouyouhui);
				$this->items = $vipgouyouhui;
				if($mode==2)
					echo json_encode($this->items);
				
			}elseif($website=='juanpi'){ // ��Ƥ�ۿ�
				if($mode==3){ // ����Ҫ�ɼ���ҳ��,�ſ�����ƷΪ45��һҳ
					$this->url = 'http://juanpi.uz.taobao.com/';
					$result = get_contents($this->url);
						
					$ptn = '/<div class="head"(.+?)class="main"/is';
					preg_match_all($ptn,$result,$jiuarr,PREG_SET_ORDER);
					//print_r($jiuarr);
						
					$ptn = '/<div class="nav-show(.+?)������Ʒ(.+?)<\/li>/is';
					preg_match_all($ptn,$jiuarr[0][0],$jiuarr1,PREG_SET_ORDER);
					//print_r($jiuarr1);
						
					$ptn = '/(\d+)/is';
					preg_match_all($ptn,$jiuarr1[0][2],$jiuarr,PREG_SET_ORDER);
					if($jiuarr[0][1]){
						return $jiuarr[0][1];
					}else{
						return false;
					}
				}else{				
					$this->url = 'http://api.juanpi.com/open/juanpi';
					$result = file_get_contents($this->url);
                                        $res = preg_replace('/jsonpReturn/i','',$result);
                                        $res = ltrim($res,'(');
                                        $res = rtrim($res,');');
                                        $res = json_decode($res,TRUE);
//                                        var_dump($res);
                                        foreach($res['goodslist'] as $k => $v){
                                                $jiuarr2[] = array('iid'=>preg_replace('/(.+?)id=/i','$3',$v['deal_taobao_link']),'nprice'=>preg_replace('/[^0-9][^0-9]/i','',$v['deal_price']),'pic'=>$v['deal_image']);
                                        }
                                        $jiukuaiyou['all'] = $jiuarr2;
//					var_dump($jiukuaiyou);						
					$this->items = $jiukuaiyou;
					if($mode==2)
						echo json_encode($this->items);
				}
			}elseif($website=='zhe800'){ // ��800
				// �ɼ����ո����Ա���Ʒǰ100��per_page=100&shop_type=0
                                
				$this->url = 'http://m.zhe800.com/m/api/deals/today?per_page=100&image_type=small&image_model=jpg&page=1&user_type=1&user_role=1&tag_url=all&parent_url_name=&shop_type=0&path_url=all';
				$result = file_get_contents($this->url);
				$zhe8arr1 = json_decode($result,TRUE);
                                foreach($zhe8arr1['objects'] as $k => $v){
                                    preg_match('/(.+?)id=(\d+)(.*?)/i',get_redirect_url_pro("http://out.zhe800.com/m/deal/".$v['id']."?iousjkl=12e54067f51deafc6a0f51b476df164d",get_redirect_url($v['wap_url'])),$matches);
                                    $zhec[] = array('iid'=>$matches[2],'nprice'=>$v['price']/100);
                                    $matches = null;
				}
				$zhe800arr['c'] = $zhec;
//                                var_dump($zhe800arr);
				// �ɼ����ո����Ա���Ʒǰ100������
				$this->url = 'http://m.zhe800.com/m/api/deals/today?per_page=100&image_type=small&image_model=jpg&page=1&user_type=1&user_role=1&tag_url=all&parent_url_name=&shop_type=1&path_url=all';
				$result = file_get_contents($this->url);
				$zhe8arr1 = json_decode($result,TRUE);
                                foreach($zhe8arr1['objects'] as $k => $v){
                                    preg_match('/(.+?)id=(\d+)(.*?)/i',get_redirect_url_pro("http://out.zhe800.com/m/deal/".$v['id']."?iousjkl=12e54067f51deafc6a0f51b476df164d",get_redirect_url($v['wap_url'])),$matches);
                                    $zhet[] = array('iid'=>$matches[2],'nprice'=>$v['price']/100);
                                    $matches = null;
				}
				$zhe800arr['t'] = $zhet;
                                
                                // �ɼ����ո�����è��Ʒǰ100������
//                                var_dump($zhe800arr);
				$this->items = $zhe800arr;	
				if($mode==2)
					echo json_encode($this->items); 
			}elseif($website=='zhuanbao'){ // ����׬��
				//$this->url = 'http://zhuanbao.uz.taobao.com/zhuanbao.php?page='.$page;
				$this->url = 'http://zhuanbao.uz.taobao.com';
				$result = get_contents($this->url);
				//echo $result;
				$zbptn = '/class="zk_main"(.+?)class="zk_inner(.+?)class="zk_page"/is';
				preg_match_all($zbptn,$result,$zbarr,PREG_SET_ORDER);
				//echo $zbarr[0][0];
				$zbptn = '/<li(.+?)class="pic_area"(.+?)<img(.+?)class="pimg(.+?)class="price"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="pr1"(.+?)<em>(.+?)<\/em>(.+?)<\/li>/is';
				preg_match_all($zbptn,$zbarr[0][0],$zbarr1,PREG_SET_ORDER);
				//print_r($zbarr1);
				foreach($zbarr1 as $k => $v){
					$zball[] = array('iid'=>$v[7],'nprice'=>$v[11]);//,'pic'=>$v['5']
				}
				$zhuanbao['zxzk'.$page] = $zball; 
				//var_dump($zhuanbao);
				$this->items = $zhuanbao;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='10mst'){ // ��ɱͨ
				$this->url = 'http://10mst.uz.taobao.com/d/seckill?cat=all&by=new&page='.$page;
				$result = get_contents($this->url);
				//echo $result;
				$mstptn = '/<div class="lx-item-list">(.+?)<div class="lx-page-area">/is';
				preg_match_all($mstptn,$result,$mstarr,PREG_SET_ORDER);				
				$mstptn = '/<div class="lx-item-list-price">(.+?)class="send"(.+?)<em>(.+?)<\/span>(.+?)class="lx-item-btn-buy"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/li>/is';				
				preg_match_all($mstptn,$mstarr[0][0],$mstarr1,PREG_SET_ORDER);
				foreach($mstarr1 as $k => $v){
					$mstall[] = array('iid'=>$v[7],'nprice'=>preg_replace('/<\/em>/','',$v[3]));
				}
				$mst['new'.$page] = $mstall;
				//var_dump($mst);
				$this->items = $mst;
				//var_dump($this->items);
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='qiang'){ // ��ţƷ
				$this->url = 'http://201314.uz.taobao.com/';
				$result = get_contents($this->url);
				$qiangptn = '/<div class="homeBody">(.+?)<div class="home_links">/is';				
				preg_match_all($qiangptn,$result,$qiangarr,PREG_SET_ORDER);
				$qiangptn = '/<div class="goodsItem">(.+?)class="price"(.+?)<\/em>(.+?)<\/span>(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/h5>/is';
				preg_match_all($qiangptn,$qiangarr[0][0],$qiangarr1,PREG_SET_ORDER);
				foreach($qiangarr1 as $k => $v){
					$qiangall[] = array('iid'=>$v[6],'nprice'=>$v[3]);
				}
				$qiang['all'] = $qiangall;
				$this->items = $qiang;
				//var_dump($this->items);
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='tealife'){ // ��ţƷ
				$this->url = 'http://appapi2.1zw.com/index/index.html';
                                for($i=1;$i<=10;$i++){
                                    $contents = "page=".$i."&&platform=Android&&version=34";
//                                    echo $contents;
                                    $opts = array(
                                        'http'=>array(
                                            'method'=>"POST",
                                            'content'=>$contents,
                                            'timeout'=>900,
//                                                'proxy'=>'tcp://222.88.236.235:80',
//                                                'request_fulluri' => true
                                    ));
                                    $context = stream_context_create($opts);
                                    $result = @file_get_contents($this->url, false, $context);
                                    $res = json_decode($result,TRUE);
                                    foreach($res['detail'] as $k => $v){
                                        $tea[] = array('iid'=>$v['product_id'],'nprice'=>$v['promo_price'],'pic'=>$v['img']);
                                    }
                                    $tealife[$i] = $tea;
                                    $tea = null;
                                    $contents = "";
                                }
                                
//				var_dump($tealife);
				$this->items = $tealife;
				//var_dump($this->items);
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='taofen8'){ // ��վ  �Է۰�
				$this->url = 'http://www.taofen8.com/';
				$result = get_contents($this->url);
				$tf8ptn = '/class="tf8_sp-1"(.+?)class="tf8_pagediv-1"/is';
				preg_match_all($tf8ptn,$result,$tf8arr,PREG_SET_ORDER);
				$tf8ptn = '/<li(.+?)class="tf8_spimg-1"(.+?)name="url_(\d+)"(.+?)class="tf8_shop"(.+?)class="tf8-index-d2"(.+?)class="tf8-d2-span2">(\d+\.?\d+)<\/span>(.+?)class="tf8-d2-span3"/is';
				preg_match_all($tf8ptn,$tf8arr[0][0],$tf8arr1,PREG_SET_ORDER);
				//print_r($tf8arr1);
				foreach($tf8arr1 as $k => $v){
					$tf8zx[] = array('iid'=>$v[3],'nprice'=>$v[7]);//,'pic'=>$v[6]
				}
				$tf8['tf8zx'] = $tf8zx;
				//var_dump($tf8);
				$this->items = $tf8;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='legou'){ // �ֹ�
				// ��ͨҳ��ɼ�
				$this->url = 'http://legou.uz.taobao.com/';
				$result = get_contents($this->url);
				$lgptn = '/<div class="recpro_list">(.+?)class="oneminute"(.+?)<ul>(.+?)<\/ul>(.+?)class="go_more"/is';
				preg_match_all($lgptn,$result,$lgarr,PREG_SET_ORDER);
				$lgptn = '/<li>(.+?)class="pro_buy"(.+?)class="price"(.+?)<\/b>(\d+\.?\d+)<\/span>(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/li>/is';
				preg_match_all($lgptn,$lgarr[0][3],$lgarr1,PREG_SET_ORDER);
				foreach($lgarr1 as $k => $v){
					$lgfq[] = array('iid'=>$v[7],'nprice'=>$v[4]);
				} 
				// �ӿڲɼ�
				/* $this->url = 'http://legou.uz.taobao.com/view/front/legouout.php';
				$result = get_contents($this->url);
				$lgptn = '/class="taeapp"(.+?)>(.+?)<\/div>(.+?)id="footer"/is';
				preg_match_all($lgptn,$result,$lgarr,PREG_SET_ORDER);
				$lgptn = '/<div class="item">(.+?)class="iid">(\d+)<\/span>(.+?)class="nprice">(\d+\.?\d+)<\/span>(.+?)class="volume">(\d+)<\/span>(.+?)<br\/>/is';
				preg_match_all($lgptn,$lgarr[0][0],$lgarr1,PREG_SET_ORDER);
				foreach($lgarr1 as $k => $v){
					$lgfq[] = array('iid'=>$v[2],'nprice'=>$v[4],'volume'=>$v[6]);
				}  */
				$legou['lgfq'] = $lgfq;
				$this->items = $legou;
				//var_dump($this->items);
				if($mode==2)
					echo json_encode($this->items); 
				//echo json_encode($this->items);
			}elseif($website=='vipzxhd'){
				$this->url = 'http://api.new0815.tuancu.com/api/item/lists';
                                
                                $contents = 'data={"key":"is_new","order":"ordid","order_by":"asc","page":1,"perPage":"200"}&token=';
//                                    echo $contents;
//                                    echo "<br />";
                                $opts = array(
                                    'http'=>array(
                                        'method'=>"POST",
                                        'content'=>$contents,
                                        'timeout'=>900,
//                                                'proxy'=>'tcp://222.88.236.235:80',
//                                                'request_fulluri' => true
                                ));
                                $context = stream_context_create($opts);
                                $result = @file_get_contents($this->url, false, $context);
                                $res = json_decode($result,TRUE);
                                foreach($res['data']['list'] as $k => $v){
                                    $vipbkrmarr[] = array('iid'=>$v['taobao_id'],'nprice'=>$v['price']);//'pic'=>$v['img']
                                }
                                $vipzxhd['tuancu'] = $vipbkrmarr;
                                $vipbkrmarr = null;
                                $contents = "";
                                
//				var_dump($vipzxhd);
				$this->items = $vipzxhd;
				if($mode==2)
					echo json_encode($this->items);
				//echo json_encode($this->items);
			}elseif($website=='tejiayitian'){
                                for($i=0;$i<=10;$i++){
                                    $this->url = 'http://m.tejiayitian.com/index/getajaxprc?p='.$i;
                                    $result = get_contents($this->url);
                                    $tjytarr = json_decode($result,TRUE);
                                    foreach($tjytarr as $k=>$v){
                                        preg_match('/biz-itemid="(\d+)"/i',file_get_contents("http://m.tejiayitian.com".$v['url']),$matchs);
                                        $tj99[] = array('iid'=>$matchs[1],'nprice'=>$v['promotion']);//,'pic'=>$v[4]
                                        $matchs = null;
                                    }
                                    $tjyt[$i] = $tj99;
                                    $tj99 = null;
                                }
//				var_dump($tjyt);    
				$this->items = $tjyt;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='tejiafengqiang'){
				$this->url = 'http://jianshi.uz.taobao.com/d/index?page='.$page;
				$result = get_contents($this->url);
				$tjfqptn = '/class="container(.+?)class="recpro_list"(.+?)class="pagination"/is';
				preg_match_all($tjfqptn,$result,$tjfqarr,PREG_SET_ORDER);
				$tjfqptn = '/<li>(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="price_list_sale"(.+?)<em>(\d+\.?\d+)<\/em>(.+?)<\/li>/is';
				preg_match_all($tjfqptn,$tjfqarr[0][2],$tjfqarr1,PREG_SET_ORDER);
				//print_r($tjfqarr1);
				foreach($tjfqarr1 as $k => $v){
					$tjfqall[] = array('iid'=>$v[3],'nprice'=>$v[7]);
				}
				$tjfq['page'.$page] = $tjfqall;
				//var_dump($tjfq); 
				$this->items = $tjfq;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='mao'){
				$this->url = 'http://www.tejiamao.com/page/xml/shouye-geilimao-9.9.xml';
				$result = file_get_contents($this->url);
                                $res = @simplexml_load_string($result,NULL,LIBXML_NOCDATA);
                                $res = json_decode(json_encode($res),true);
				foreach($res['tejiamao']['item'] as $k => $v){
					$mao99[] = array('iid'=>$v['iid'],'nprice'=>$v['tejia'],'pic'=>$v['img']);
				}
                                $tejiamao['mao99'] = $mao99;
                                
                                $this->url = 'http://www.tejiamao.com/page/xml/shouye-geilimao-19.9.xml';
				$result = file_get_contents($this->url);
                                $res = @simplexml_load_string($result,NULL,LIBXML_NOCDATA);
                                $res = json_decode(json_encode($res),true);
				foreach($res['tejiamao']['item'] as $k => $v){
					$mao199[] = array('iid'=>$v['iid'],'nprice'=>$v['tejia'],'pic'=>$v['img']);
				}
                                $tejiamao['mao199'] = $mao199;
                                
                                $this->url = 'http://www.tejiamao.com/page/xml/shouye-geilimao-zhe.xml';
				$result = file_get_contents($this->url);
                                $res = @simplexml_load_string($result,NULL,LIBXML_NOCDATA);
                                $res = json_decode(json_encode($res),true);
				foreach($res['tejiamao']['item'] as $k => $v){
					$maozhe[] = array('iid'=>$v['iid'],'nprice'=>$v['tejia'],'pic'=>$v['img']);
				}
                                $tejiamao['maozhe'] = $maozhe;
                                
//                                var_dump($tejiamao);
				$this->items = $tejiamao;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='mizheuz'){
				$this->url = 'http://m.mizhe.com/tuan/10yuan-all---1-100---1.html';
				$result = get_url_content($this->url);
                                $mizheuzarr = json_decode($result,TRUE);
				foreach($mizheuzarr['tuan_hot_items'] as $k => $v){
                                    $mizhehot[] = array('iid'=>$v['num_iid'],'nprice'=>$v['price']/100);
				}
                                $mizhe['hot'] = $mizhehot;
                                foreach($mizheuzarr['tuan_items'] as $k => $v){
                                    $mizhe9[] = array('iid'=>$v['num_iid'],'nprice'=>$v['price']/100);
				}
                                $mizhe['mizhe'] = $mizhe9;
//				var_dump($mizhe);
				$this->items = $mizhe;
				if($mode==2)
					echo json_encode($this->items);
				
			}elseif($website=='ztbest'){
				$this->url = 'http://ztbest.uz.taobao.com';
				$result = get_contents($this->url);
				$ztbestptn = '/class="taeapp_aw2"(.+?)class="taeapp_aw"/is';
				preg_match_all($ztbestptn,$result,$ztbestarr,PREG_SET_ORDER);
				$jryxR =  $ztbestarr[0][1]; // ������ѡ
				$pzgR = $ztbestarr[1][1]; // Ʒ�ʹ�
				$fkmsR = $ztbestarr[2][1]; // �����ɱ
				
				$ztbestarr = null;
				// ������ѡ
				$ztbestptn = '/class="taeapp_box1"(.+?)class="taeapp_box_pic"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<img(.+?)src="(.+?)"(.+?)class="taeapp_box_price"(.+?)<strong>(\d+\.?\d+)<\/strong>/is';
				preg_match_all($ztbestptn,$jryxR,$ztbestarr,PREG_SET_ORDER);
				foreach($ztbestarr as $k => $v){
					$jryx[] = array('iid'=>$v[4],'nprice'=>$v[11],'pic'=>$v[8]);
				}
				$ztbest['jryx'] = $jryx;
				// END - ������ѡ
				
				$ztbestarr = null;
				// Ʒ�ʹ�
				$ztbestptn = '/class="taeapp_box1"(.+?)class="taeapp_box_pic"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<img(.+?)src="(.+?)"(.+?)class="taeapp_box_price"(.+?)<strong>(\d+\.?\d+)<\/strong>/is';
				preg_match_all($ztbestptn,$pzgR,$ztbestarr,PREG_SET_ORDER);
				foreach($ztbestarr as $k => $v){
					$pzg[] = array('iid'=>$v[4],'nprice'=>$v[11],'pic'=>$v[8]);
				}
				$ztbest['pzg'] = $pzg;
				// END - Ʒ�ʹ�
				
				$ztbestarr = null;
				// �����ɱ
				$ztbestptn = '/class="taeapp_box_pic"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<img(.+?)src="(.+?)_200x200.jpg"(.+?)class="taeapp_box_price"(.+?)<span>��(\d+\.?\d+)<\/span>/is';
				preg_match_all($ztbestptn,$fkmsR,$ztbestarr,PREG_SET_ORDER);
				foreach($ztbestarr as $k => $v){
					$fkms[] = array('iid'=>$v[3],'nprice'=>$v[10],'pic'=>$v[7].'_310x310.jpg');
				}
				$ztbest['fkms'] = $fkms;
				// END - �����ɱ
				
				//var_dump($ztbest);
				$this->items = $ztbest; 
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='mmrizhi'){
				$this->url = 'http://www.mmgou.org/item.php';
				$result = get_contents($this->url);
				$mmrzptn = '/<tr>(.+?)<td(.+?)id="img">(.+?)<\/td>(.+?)<td(.+?)id="tejia">(.+?)<\/td>(.+?)<td(.+?)id="iid">(.+?)<\/td>(.+?)<\/tr>/is';
				preg_match_all($mmrzptn,$result,$mmrzarr,PREG_SET_ORDER);
				//print_r($mmrzarr);
				foreach($mmrzarr as $k => $v){
					$allsp[] = array('iid'=>$v[9],'nprice'=>$v[6],'pic'=>$v[3]);
				}
				
				$mmrizhi['all'] = $allsp; 
				//var_dump($mmrizhi);
				$this->items = $mmrizhi;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='yuansu'){
				$this->url = 'http://yuansu.uz.taobao.com/view/baohuasuan.php';
				$result = get_contents($this->url);
				$yuansuptn = '/<table(.+?)class="img">(.+?)<\/td>(.+?)class="tejia">(.+?)<\/td>(.+?)class="iid">(.+?)<\/td>(.+?)<\/table>/is';
				preg_match_all($yuansuptn,$result,$yuansuarr,PREG_SET_ORDER);
				
				//var_dump($yuansuarr);
				foreach($yuansuarr as $k => $v){
					$bkjp[] = array('iid'=>$v[6],'nprice'=>$v[4],'pic'=>$v[2]);//,'pic'=>preg_replace('/_210x210.jpg/i','_310x310.jpg',$v[8])
				}
				$bkjp1 = array_chunk($bkjp,12);
				$bkjq2 = $bkjp1[0];
				$yuansu['all'] = $bkjq2; 
				// END - ���Ʒ
				
				//var_dump($yuansu);
				$this->items = $yuansu;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='fengtao'){
				/* $this->url = 'http://fengtao.uz.taobao.com';
				$result = get_contents($this->url);
				$jrftptn = '/class="item_sy"(.+?)/is';
				preg_match_all($jrftptn,$result,$jrftarr,PREG_SET_ORDER);
				print_r($jrftarr);
				$this->items = $jrft;
				if($mode==2)
					echo json_encode($this->items); */
			}elseif($website=='youpinba'){
				$this->url = 'http://youpinba.yimiaofengqiang.com/main/ju';
				$result = get_contents($this->url);
				$qypptn = '/class="iid">(\d+)<\/td>(.+?)class="nprice">(\d+\.?\d+)<\/td>(.+?)class="pic">(.+?)<\/td>/is';
				preg_match_all($qypptn,$result,$qyparr,PREG_SET_ORDER);
				//print_r($qyparr);
				foreach($qyparr as $k => $v){
					$qyp[] = array('iid'=>$v[1],'nprice'=>$v[3],'pic'=>$v[5]);
				}
				$youpinba['all'] = $qyp;
				$this->items = $youpinba;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='shiyonglianmeng'){
				$this->url = 'http://shiyonglianmeng.uz.taobao.com/view/front/ju.php';
				$result = get_contents($this->url);
				$sylmptn = '/class="iid">(\d+)<\/td>(.+?)class="nprice">(\d+\.?\d+)<\/td>(.+?)class="pic">(.+?)<\/td>/is';
				preg_match_all($sylmptn,$result,$sylmarr,PREG_SET_ORDER);
				//print_r($qyparr);
				foreach($sylmarr as $k => $v){
					$f123[] = array('iid'=>$v[1],'nprice'=>$v[3],'pic'=>$v[5]);
				}
				$sylm['all'] = $f123;
				$this->items = $sylm;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='jiejie'){
//				$this->url = 'http://api.new0815.tuancu.com/api/item/lists';
//				$result = file_get_contents($this->url);
//                                echo $this->url;
//				$res = json_decode($result,TRUE);
//                                var_dump($res);
//				foreach($zxhdarr as $k => $v){
//					$yptharr[] = array('iid'=>$v[4],'nprice'=>$v[10],'pic'=>$v[7]);
//				}
//				$vipzxhd['ypth'] = $yptharr;
				//var_dump($yptharr);
				// end - ʵ���Ƽ� && ��Ʒ�ػ�
				
				//var_dump($vipzxhd);
//				$this->items = $vipzxhd;
				if($mode==2)
					echo json_encode($this->items);
				//echo json_encode($this->items);
			}elseif($website=='ifengqiang'){
				$this->url = 'http://ifengqiang.uz.taobao.com/view/front/outzhaoshang.php';
				$result = get_contents($this->url);
				$sylmptn = '/class="iid">(\d+)<\/td>(.+?)class="nprice">(\d+\.?\d+)<\/td>(.+?)class="pic">(.+?)<\/td>/is';
				preg_match_all($sylmptn,$result,$sylmarr,PREG_SET_ORDER);
				//print_r($qyparr);
				foreach($sylmarr as $k => $v){
					$f123[] = array('iid'=>$v[1],'nprice'=>$v[3],'pic'=>$v[5]);
				}
				$sylm['all'] = $f123;
				$this->items = $sylm;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='zhekouba'){
				$this->url = 'http://www.432gou.com/?c=main&a=outzs';
				$result = get_contents($this->url);
				$zhekouba = json_decode($result,true);
				//var_dump($zhekouba);
				$this->items = $zhekouba;
				if($mode==2)
					echo json_encode($this->items);
				
			}elseif($website=='aitaoba'){
				$this->url = 'http://aitaoba.uz.taobao.com';
				$result = get_contents($this->url);
				$atbptn = '/class="show1"(.+?)class="good-title"(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)class="price-current"(.+?)<\/em>(.+?)<\/span>(.+?)<\/li>/is';
				preg_match_all($atbptn,$result,$atbarr,PREG_SET_ORDER);
				foreach($atbarr as $k => $v){
					$atb[] = array('iid'=>$v[4],'nprice'=>$v[8]);
				}
				$atbcj['all'] = $atb;
				$this->items = $atbcj;
				if($mode==2)
					echo json_encode($this->items);
			}elseif($website=='bujie'){
				$this->url = 'http://www.bujie.com/api/bujie';
				$result = get_contents($this->url);
				$ptn = '/<item><num_iid>(\d+)<\/num_iid>(.+?)<coupon_price>(\d+\.?\d+)<\/coupon_price>(.+?)<pic_url>(.+?)<\/pic_url>(.+?)<\/item>/is';
				preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
				foreach($arr as $k => $v){
					$bj[] = array('iid'=>$v[1],'nprice'=>$v[3],'pic'=>$v[5]);
				}
				$bujie['all'] = $bj;
//				var_dump($bujie);
				$this->items = $bujie;
				if($mode==2)
					echo json_encode($this->items);
				
			}
			
		}
	}
	
	/*
	 * ����ɼ��õ���������
	*/
	public function getitems(){
		return $this->items;
	}
	

}
	
?>
