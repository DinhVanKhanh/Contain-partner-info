const _url_init = 'src/index.php';
const _controller = 'demo';
const _dir_banner = "../data_files/";

// Load Todohuken by area
function loadTodouhukenListByAreaId( areaId ) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadTodohukenListByAreaId',
            areaId: Number( areaId ),
        },
        beforeSend: function () {
			$('.loading').removeClass('hide');
		},
        success: function ( data ) {
            $('#toTag').html( data.view );
            $('#toTag').removeClass('hide');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('todouhukenのロードのエラーであります');
        },
        complete: function () {
            $('.loading').addClass('hide');
        }
    });
}

// Load Todohuken by shop
function loadTodouhukenByShopId( id ) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'loadTodohukenByShopId',
            shopId: id
        },
        beforeSend: function () {
			$('.loading').removeClass('hide');
		},
        success: function (data) {
            $('#toTag').html( data.view );
            $('#toTag').removeClass('hide');
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('サーバーへの接続のエラーであります');
        },
        complete: function () {
            $('.loading').addClass('hide');
        }
    });
}

// Search schedule
function searchSchedule( areaId, shopId, todouhukenId, scheduleDate, address ) {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'searchSchedule',
            areaId: Number( areaId ),
            shopId: Number( shopId ),
            todouhukenId: Number( todouhukenId ),
            date: scheduleDate,
            address: address
        },
        success: function (data) {
            $('#tagTable').append( data.reloadDate );
            $('#tagTable').html( data.view );

            if ( !flagLoaded ) {
                // Get events
                events = data.events;

                // Load autocomplete
                data.autoData.forEach( function ( item, index, arr ) {
                    arr[index] = unescapeHtml( item );
                });

                $("#searchAddress").autocomplete({
                    source: data.autoData,
                });

                flagLoaded = true;
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_search').html('見つかりません');
        }
    });
}

// Get Banner
function getBanner(parentId, isShop) {
    $("#banner1").attr("src", "");
    $("#banner2").attr("src", "");
    $("#banner3").attr("src", "");
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getBanner',
            parentId: parentId,
            isShop: isShop
        },
        success: function ( data ) {
            if ( Object.keys(data).length > 0 ) {
                let banner1 = (data.Banner1 != "") ? _dir_banner + data.Banner1 : "";
                $("#banner1").attr("src", banner1);

                let banner2 = (data.Banner2 != "") ? _dir_banner + data.Banner2 : "";
                $("#banner2").attr("src", banner2);

                let banner3 = (data.Banner3 != "") ? _dir_banner + data.Banner3 : "";
                $("#banner3").attr("src", banner3);
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_search').html('Fail search !');
            $('.loading').addClass('hide');
        }
    });
}

// ChangeArea
function ChangeArea(id) {
    // Reload banner
    getBanner(id, 0);

    // Init search condition
    $('#searchDate').val('');
    $('#searchAddress').val('');

    // Set active tab
    $('#tabArea li').removeClass('atv');
    $('#tabArea li a#lnk' + id).parents('li').addClass('atv');

    // Load combo 都市
    loadTodouhukenListByAreaId(id);

    // Load grid
    flagLoaded = false;
    searchSchedule(id, null, null, null, null);

    // Set areaId
    $('#hdAreaId').val(id);
    setTimeout(function () {
        fixFooter();
    }, 200);
}


// Change Shop
function ChangeShop(id) {
    // Reload banner
    getBanner(id, 1);

    // Init search condition
    $('#searchDate').val('');
    $('#searchAddress').val('');

    // Set active tab
    $('#tabArea li').removeClass('atv');
    $('#tabArea li a#lnk' + id).parents('li').addClass('atv');

    // Load combo 都市
    loadTodouhukenByShopId(id);

    // Load grid
    flagLoaded = false;
    searchSchedule(null, id, null, null, null);
    $('#hdShopId').val(id);
    setTimeout(function () {
        fixFooter();
    }, 200);
}

// Init tab Area
function initAreaTab() {
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getAreaList'
        },
        success: function ( data ) {
            data.forEach( function ( item ) {
                let viewTab = ( item.AreaId == $('#hdAreaId').val() ) ? '<li class="atv">' : '<li>';
                viewTab += '<a id="lnk' + item.AreaId + '" onclick="ChangeArea(' + item.AreaId + ');" href="javascript:void(0);">' +
                                item.AreaName +
                            '</a>';
                viewTab += '</li>';
                $("#tabArea").append( viewTab );
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_search').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}

// Init tab Shop special
function initShopTab() {
    $("#tabArea").addClass("left");
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getShopName',
        },
        success: function ( data ) {
            data.forEach( function (item) {
                let viewTab = ( item.ShopId == $('#hdShopId').val() ) ? '<li class="atv">' : '<li>';
                viewTab += '<a id="lnk' + item.ShopId + '" onclick="ChangeShop(' + item.ShopId + ');" href="javascript:void(0);">' +
                                item.Name +
                            '</a>';
                viewTab += '</li>';
                $("#tabArea").append( viewTab );
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_search').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}

// Init
function init() {
    let areaId = $('#hdAreaId').val();
    let shopId = $('#hdShopId').val();

    if (areaId != "") {
        getBanner(areaId, 0);
        initAreaTab();
        loadTodouhukenListByAreaId( areaId );
    }
    else {
        getBanner(shopId, 1);
        initShopTab();
        loadTodouhukenByShopId( shopId );
    }
    searchSchedule( areaId, shopId, null, null, null );

    // Button Events
    $("#btnSearch").click(function() {
        searchSchedule(
            $('#hdAreaId').val(),
            $('#hdShopId').val(),
            $('#searchTo').val(),
            $('#searchDate').val(),
            $('#searchAddress').val()
        );
    });
}

/**
 * Get list special shops and set select by id
 * @return Combobox
 */
function getListSpecialShop( activeId ) {
    $('#shopId option').remove();
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getListSpecialShop',
        },
        success: function (data) {
                let str = "";
                data.forEach( function ( item ) {
                    str += "<option value='" + item.ShopId + "'>" + item.Name + "</option>";
                });

                $('#shopId').html( str );

                if ( activeId == "undefined" ) {
                    $('#shopId').val($("#shopId option:first").val());
                }
                else {
                    $('#shopId').val(activeId);
                }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}

/**
 * Get list normal shops and set select by id
 * @return Combobox
 **/
function getListNormalShop( activeId ) {
    $('#shopId option').remove();
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: _url_init,
        data: {
            controller: _controller,
            action: 'getListNormalShop',
        },
        success: function (data) {
            let str = "";
            data.forEach( function ( item ) {
                str += "<option value='" + item.ShopId + "'>" + item.Name + "</option>";
            });
            $('#shopId').html( str );

            if ( activeId == "undefined" ) {
                $('#shopId').val( $("#shopId option:first").val() );
            }
            else {
                $('#shopId').val( activeId );
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            $('#error_load_to').html('サーバーへの接続のエラーであります');
            $('.loading').addClass('hide');
        }
    });
}
