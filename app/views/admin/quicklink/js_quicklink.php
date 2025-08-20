<audio id="myAudio">
  <source src="<?php echo base_url() ?>assets/sound/voucher_akan_habis.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/push/push.min.js') ?>"></script>
<script>
// register service worker:
// navigator.serviceWorker.register("<?php //echo base_url('assets/notif/service-worker.js') ?>")
//     .then(reg => console.log('SW registered!', reg))
//     .catch(err => console.log('Boo!', err));

//   setTimeout(() => {
//     const img = new Image();
//     img.src = '<?php //echo base_url() ?>assets/inspinia271/img/favicon/android-chrome-192x192.png';
//     document.body.appendChild(img);
//   }, 3000);

    
    var status = false;
    var pesan = "";
    $.getJSON("<?php echo site_url('notif') ?>", function(data, status){
        pesan = data.msg;
        status = data.status;
        console.log(status);
        if (status == true) {
            // play sound
            let gvoice = document.querySelector("audio#myAudio");
            gvoice.play();
            // show push notification
            Push.create("Voucher akan habis!", {
                body: pesan,
                icon: '<?php echo base_url() ?>assets/inspinia271/img/favicon/android-chrome-192x192.png',
                timeout: 30000,
                onClick: function () {
                    window.open("<?php echo site_url('dashboard/konten')?>");
                    this.close();
                }
            });
        }
    });
    
</script>
</body>

</html>
