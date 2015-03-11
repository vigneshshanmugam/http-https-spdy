<?php
header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

$noOfImages = 30;
$imageSize = 5;
if(!empty($_GET['imgNo'])){
    $noOfImages = $_GET['imgNo'];
}

if(!empty($_GET['size'])){
    $imageSize = $_GET['size'];
}
?>
<!DOCTYPE>
<html>
<head>
    <title>SPDY Test Page</title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="Keywords" content=""/>
    <meta name="Description" content=""/>
    <link rel="stylesheet" type="text/css" href="static/main.css">
</head>
<body>
  <div class="center">
      <h1>Enhanced HTTP vs HTTPS(SPDY) Test</h1>
  </div>
  <div class="test-params center tmargin20">
      <div class="sizes">
          <span>Select Image Size </span>
          <a href="" class="1kb selected" data-image-size="5">5kB</a>
          <a href="" class="10kb" data-image-size="10">10kB</a>
          <a href="" class="50kb" data-image-size="50">50kB</a>
          <a href="" class="100kb" data-image-size="100">100kB</a>

      </div>
      <div class="no-of-images">
          Enter Number Of Images:  <input type="text" value="<?php echo $noOfImages;?>" />
      </div>
      <div class="refresh-cont tmargin20">
          Refresh the button to load the test with selected params
          <a href="" class="refresh-btn lmargin10 fk-font-16">Refresh</a>    
      </div>
  </div>

  <div class="images-container tmargin20">
      <?php for($i=0; $i<$noOfImages; $i++) {
         $randNo = rand(pow(10, 8-1), pow(10, 8)-1) + $i; ?>
         <img src="static/images/<?php echo $imageSize;?>kb.jpeg?id=<?php echo $randNo;?>"/>
      <?php } ?>
  </div>
  <div class="metrics-container">
      <h2 class="fk-font-18">Performance Metrics of the Experiment</h2>
  </div>
  <script type="text/javascript">
      (function(){
          var reloadPage = function(){
              var noOfImages = $('.test-params :input')[0].value;
              var sizeOfImage = $('.test-params a.selected').data().imageSize;
              var url = window.location.href;
              url = url + '?imgNo='+ noOfImages + '&size=' + sizeOfImage;
              window.location.href = url;
          };

          $('.test-params .refresh-btn').on('click',function(e){
              e.preventDefault()
              reloadPage();
          });

          $('.test-params a').on('click',function(e){
              e.preventDefault();
              var $this = $(this);
              $this.parent().find('a').removeClass('selected')
              $this.addClass('selected');
          });

          function constructHTML(timings){
              var d = document, p, i, v;
              var container = d.createElement('div');
              for(i = 0; i < timings.length; i++){
                  p = d.createElement('p');
                  v = timings[i].label + ' : ' + timings[i].time;
                  $(p).html(v);
                  $(container).append($(p));
              }
              $('.metrics-container').append($(container));
          }

          function collectMetrics(){
               var t = window.performance.timing;
               var lt = window.chrome && window.chrome.loadTimes && window.chrome.loadTimes();
               var timings = [];
                timings.push({
                  label: "Domain Lookup Time",
                  time: t.domainLookupEnd - t.domainLookupStart + "ms"
                });
                timings.push({
                  label: "TCP Connection Time",
                  time: t.connectEnd - t.connectStart + "ms"
                });
                if(t.secureConnectionStart){
                    timings.push({
                      label: "SSL Connection Time",
                      time: t.connectEnd - t.secureConnectionStart + "ms"
                    });
                }
                
                timings.push({
                  label: "Total Response Time",
                  time: t.responseEnd - t.requestStart + "ms"
                });
                timings.push({
                  label: "DOM Content Loaded",
                  time: t.domContentLoadedEventEnd - t.navigationStart + "ms"
                });
                timings.push({
                  label: "Page Load Time",
                  time: t.loadEventEnd - t.navigationStart + "ms"
                });
                if(lt) {
                  if(lt.wasNpnNegotiated) {
                    timings.push({
                      label: "NPN negotiation protocol",
                      time: lt.npnNegotiatedProtocol
                    });
                  }
                  timings.push({
                    label: "Connection Info",
                    time: lt.connectionInfo
                  });
                  timings.push({
                    label: "First paint after Document load",
                    time: Math.ceil(lt.firstPaintTime - lt.finishDocumentLoadTime) + "ms"
                  });
                }
                constructHTML(timings);
          }
          setTimeout(function(){
              collectMetrics();
          },0);
      })();
  </script>
</body>
</html>
