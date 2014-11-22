<div class="area-top clearfix">
    <div class="pull-left header">
        <h3 class="title">
            <i class="icon-user"></i>
            Users
        </h3>
    </div>
</div>

<div class="row">
<div class="col-md-12">
<div class="box">
<div class="box-content">
    <table cellpadding="0" cellspacing="0" border="0" class="dTable" id="users"></table>
</div>
</div>
</div>
</div>
<script>
    var dataSet = [[]];

    $(document).ready(function() {
        $("#users").dataTable({
            "bJQueryUI": false,
            "bAutoWidth": false,
            "sPaginationType": "full_numbers",
            "sDom": "<\"table-header\"fl>t<\"table-footer\"ip>",
            "ajax": "/users/listAll",
            "columns": [
                {
                    "title": "Name",
                    "data": "username",
                    "render": function( data, type, row ) {
                        return "<a href='/conversations/addMessageByReceiver/" + row.id + "'>" + row.username + "</a>";
                    }
                },
                {
                    "title": "Email",
                    "data": "email"
                }
            ]
        });
    } );

</script>