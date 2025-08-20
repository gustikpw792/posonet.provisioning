<script src="<?php echo base_url('assets/inspinia271/js/plugins/dataTables/datatables.min.js') ?>"></script>
<script src="<?php echo base_url('assets/inspinia271/js/plugins/select2/select2.full.min.js') ?>"></script>


<script>
    var wilayah = $('.multiSelect').select2({
        placeholder: "Pilih Wilayah",
        width: "100%",
        // dropdownParent : $('#myModal')
    });


    $(document).ready(function() {
        getRekening();

        $('[name="level"]').load("<?= site_url('getselect/get_enum_values/users/level') ?>");
        $('[name="id_karyawan"]').load("<?= site_url('getselect/pilih_mul/karyawan/id_karyawan/nama_lengkap') ?>");
        $('.multiSelect').load("<?= site_url('getselect/pilih_mul_dua/wilayah/id_wilayah/kode_wilayah/wilayah') ?>");


        tableUsers = $('#tableUsers').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?php echo site_url('settings/ajax_list')?>",
                "type": "POST"
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {
                "targets": [ -1 ], //last column
                "orderable": false, //set not orderable
            },
            ],

            // pageLength: 25,
            info: false,
            paging: false,
            responsive: true,
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                {
                text: '<span class="text-success"><i class="fa fa-plus"></i> Tambah</span>',
                action: function ( e, dt, node, config ) {
                    adds();
                }
                },
                
            ]

        });
    });

    var save_method;

    function reload_table(){
        tableUsers.ajax.reload(null,false); //reload datatable ajax
    }

    function adds()
    {
        save_method = 'add_user';
        $('#form')[0].reset(); // reset form on modals
        $('[name="id_paket"]').val('');
        $('#myModalUser').modal('show'); // show bootstrap modal
        $('.help-block').empty();
        $('.fokus').focus();
        $('.modal-title').text('Tambah User'); // Set Title to Bootstrap modal title
        $("#btnSaveUsers").text('Saved').attr('disabled',false);
        wilayah.val('').trigger('change');
    }

    function getRekening(){
        $.get("<?= site_url('settings/get_rekening') ?>",
        function(data, status) {
            $('[name="nama_bank"]').val(data[0]);
            $('[name="no_rekening"]').val(data[2]);
            $('[name="nama_pemilik_pekening"]').val(data[1]);

            console.log(data);
        }, 'json');
    }
    
    function getTgBot(){
        $.get("<?= site_url('settings/get_tg_bot') ?>",
        function(data, status) {
            $('[name="tg_base_url"]').val(data['tg_base_url']);
            $('[name="tg_token"]').val(data["tg_token_bot"]);
            $('[name="tg_username"]').val(data["tg_username_bot"]);
            $('[name="tg_chat_id_admin"]').val(data["tg_chat_id_admin"]);
            $('[name="tg_chat_id_teknisi"]').val(data["tg_chat_id_teknisi"]);
            $('[name="tg_chat_id_group"]').val(data["tg_chat_id_group"]);

            console.log(data);
        }, 'json');
    }

    function save(settings){
        if(settings == 'rekening'){
            $.post(
                "<?= site_url('settings/save_rekening') ?>", {
                    bank: $('[name="nama_bank"]').val(),
                    norek: $('[name="no_rekening"]').val(),
                    nama_pemilik_pekening: $('[name="nama_pemilik_pekening"]').val(),
                },
                function(data, status) {
                    if (status) {
                        console.log(data.message);
                        alert(data.message);
                        $("#btnSaveRek").text('Saved').attr('disabled',true);
                    }
                },
                'json')
            }
            if(settings == 'users'){

                $("#btnSaveUsers").text('Updating').attr('disabled',true);

                if($('[name="password"]').val() == '') {
                    alert('Masukan password baru!');
                    return;
                }
                var url;
                if(save_method == 'add_user') {
                    url = "<?php echo site_url('settings/save_users')?>";
                } else {
                    url = "<?php echo site_url('settings/update_users')?>";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $("#form_users").serialize(),
                    dataType: "JSON",
                    success: function(data) {
                        if (data.status) {
                            console.log(data.message);
                            alert(data.message);
                            $("#btnSaveUsers").text('Save').attr('disabled',false);
                            reload_table();
                            $('#myModalUser').modal('hide'); // hide bootstrap modal

                        }
                    },
                    error: function(e){

                    }
                });

            }
    }

    function delete_user(id){
        if(confirm('Are you sure delete this data?'))
        {
            $.ajax({
            url : "<?php echo site_url('settings/delete_user')?>/"+id,
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                notif('Berhasil menghapus data!','Sukses','success');
                reload_table();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                notif('Gagal menghapus data!','Error','error');
            }
            });

        }
    }

    function edit_user(id){
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals
        $.ajax({
            url : "<?php echo site_url('settings/get_edit_user/')?>" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id_users"]').val(data.id_users);
                $('[name="username"]').val(data.username);
                $('[name="password"]').val('');
                $('[name="id_karyawan"]').val(data.id_karyawan);
                $('[name="level"]').val(data.level);
                $('[name="aktif"]').val(data.aktif);
                wilayah.val(data.akses_wilayah).trigger('change');
                $('#myModalUser').modal('show');
                $('.modal-title').text('Edit User');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            notif('Gagal mengambil data!','Error','error');
            }
        });
    }

    function upperCase() {
        const x = document.getElementById("bank");
        x.value = x.value.toUpperCase();
    }
</script>