function odtreeview(obj) {
    this.id = obj.attr('id');
    this.form   = obj.data('form');
}

odtreeview.prototype = {
    getData: function (evt) {
        let selected = [];
        $('#'+this.id+' li.selected').each(function () {
            selected.push($(this).find('input').data('id'));
        });
        let chps = "id=" + this.id + "&value='" + selected.join("$") + "'&event='click'";
        return chps;
    },
    setData: function (data) {
        $.each(data, function (i, value) {
            $('#'+this.id+' li[data-lvl="'+ value.lvl +'"][data-ord="'+ value.ord +'"]').addClass('selected');
        });
    },
    updtTreeLeaf(params) {
        let html        = params['html'];
        let selector    = params['selector'];
        $('#'+this.id+' '+selector).replaceWith(html);

    },
    appendTreeNode(params) {
        let html        = params['html'];
        let selector    = params['selector'];
        $('#'+this.id+' '+selector).append(html);
    },
};