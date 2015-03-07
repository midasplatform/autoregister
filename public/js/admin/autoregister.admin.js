var midas = midas || {};
midas.autoregister = midas.autoregister || {};

midas.autoregister.invertRow = function(source, communityId) {
    'use strict';
    var dest = source === 'targeted' ? 'ignored' : 'targeted';
    var tr = $('#'+source+'CommunityRow'+communityId).remove();
    var actionA = tr.find('a[qtip]');
    var imgSrc = actionA.find('img').attr('src');
    if (source === 'ignored') {
        actionA.attr('qtip', 'Remove community from autoregister targeted list');
        actionA.find('img').attr('src', imgSrc.replace('add', 'close'));
    } else {
        actionA.attr('qtip', 'Add community to autoregister targeted list');
        actionA.find('img').attr('src', imgSrc.replace('close', 'add'));
    }
    actionA.removeAttr('onclick');
    var registerAction = source === 'targeted' ? 'target' : 'ignore';
    actionA.unbind('click').click(function () {
        midas.autoregister.registerCommunity(communityId, registerAction);
    });
    tr.attr('id', tr.attr('id').replace(source, dest));
    var destTable = '#'+dest+'ListTable';
    var prevRow = $(destTable + ' tbody tr:last');
    var stripeClass = prevRow.length === 0 || prevRow.attr('class') === 'even' ? 'odd' : 'even';
    tr.attr('class', stripeClass);
    $(destTable).append(tr);
    // restyle source table
    var sourceTable = '#'+source+'ListTable';
    $(sourceTable+' tbody tr').each(function(i, tr) {
        $(this).attr('class', i % 2 == 0 ? 'odd' : 'even');
    });
    return tr;
}

midas.autoregister.registerCommunity = function(communityId, registerAction) {
    'use strict';
    ajaxWebApi.ajax({
        method: 'midas.autoregister.register.community',
        args: 'communityId=' + communityId + '&register='+registerAction,
        success: function(response) {
            var communityId = response.data.community_id;
            midas.autoregister.invertRow(registerAction === 'ignore' ? 'targeted' : 'ignored', communityId);
        }
    });
}
