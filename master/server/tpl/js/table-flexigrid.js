$(function() {
    $('.all-table').flexigrid({
//        height:'auto'
//        striped:false,
//        url: 'post2.php',
//        dataType: 'json',
        colModel : [
            {display: 'ID', name : 'ID', width : 40, sortable : true, align: 'center'},
            {display: 'ID_OBJ', name : 'ID_OBJ', width : 180, sortable : true, align: 'left'},
            {display: 'SDATE', name : 'SDATE', width : 120, sortable : true, align: 'left'},
            {display: 'MES', name : 'MES', width : 130, sortable : true, align: 'left', hide: true},
            {display: 'mes_type', name : 'mes_type', width : 80, sortable : true, align: 'right'},
            {display: 'ID_MESGRP', name : 'ID_MESGRP', width : 80, sortable : true, align: 'right'}
        ]
//        buttons : [
//            {name: 'Add', bclass: 'add', onpress : test},
//            {name: 'Delete', bclass: 'delete', onpress : test},
//            {separator: true}
//        ],
//        searchitems : [
//            {display: 'ISO', name : 'iso'},
//            {display: 'Name', name : 'name', isdefault: true}
//        ],
//        sortname: "iso",
//        sortorder: "asc",
//        usepager: true,
//        title: 'Countries',
//        useRp: true,
//        rp: 15,
//        showTableToggleBtn: true
//        width: 700,
//        height: 200
    });
});