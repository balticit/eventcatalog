<script type="text/javascript" src="/js/jquery-1.3.2.js"></script>
            <div>Идет рассылка...</div>
            <div>Пожалуйста, не закрывайте это окно и не отключайтесь от интернета, пока рассылка не завершится</div>
            <div>Отослано <span id="mailSended">0</span> писем из <?php echo $totalcount ?></div>
            <br/>
            <div style="border:1px solid silver;width:300px;padding:10px" align="left">
               <div id="progressProcent" style="padding-bottom:10px" align="center">1 %</div>
               <div align="left">
                 <div id="progressBar" style="background: #000;width:1%">&nbsp;</div>
               </div>
            </div>
           <div id="log" style="padding:10px">
            </div>
            <script type="text/javascript">
               var indexValue = 1;
               var totalValue = <?php echo $totalcount ?>;
               
               function logError(msg) {
                 var log = $('#log').html();
                 log = log +  msg + "<br/>";
                 $('#log').html(log);    
               }
            
            function onSendMail(data) {
               $('#mailSended').html(data.index);
               if (data.errors != null) {
                   for (var i = 0; i < data.errors.length; i++) {
                       logError(data.errors[i]);
                   }
               }
               if (data.index < totalValue) {
                    indexValue = data.index;
                    var width = Math.round((indexValue/totalValue)*100) + "%"; 
                    $('#progressProcent').html(width);
                    $('#progressBar').css('width',width);
                    var timeout = Math.ceil(Math.random()*300);
                    setTimeout(function(){
                       $.ajax({url: "/cms/ajaxsendmail",
                              global: false,
                              type: "POST",
                              data: ({index: indexValue,subject: '<?php echo $subject ?>',mailheader: '<?php echo $add_header ?>',filter: "<?php echo $sendfilter ?>", user_subscribed: "<?php echo $user_subscribed ?>"}),
                              dataType: "json",
                              timeout: 15000,
                              success: function(msg){
                                 onSendMail(msg);
                              },
                              error: function (XMLHttpRequest, textStatus, errorThrown) {
                                      logError("Request failed. Status: " + textStatus+"; Waiting 2 sec.");
                                      indexValue = indexValue + 1;
                                      setTimeout(function(){
                                          logError('Resuming from: ' + indexValue);
                                          onSendMail({'index':indexValue});
                                      },2000);
                                      
                                    }
                           }
                        )
                    },timeout); 
               }
               else {
                   $('#progressProcent').html("100%");
                   $('#progressBar').css('width','100%');
               }
            }
            
            $(document).ready(function () {
                onSendMail({'index':0});
            });
            
            </script>