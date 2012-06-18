Ext.ux.Portal = Ext.extend(Ext.Panel, {
    layout: 'column',
    autoScroll:true,
    cls:'x-portal',
    defaultType: 'portalcolumn',

    initComponent : function(){
        Ext.ux.Portal.superclass.initComponent.call(this);
        this.addEvents({
            validatedrop:true,
            beforedragover:true,
            dragover:true,
            beforedrop:true,
            drop:true
        });
        this.adjustForScrollbar();
        this.on('resize', function() {this.lastCW = this.body.dom.clientWidth;}, this);
    },

    initEvents : function(){
        Ext.ux.Portal.superclass.initEvents.call(this);
        this.dd = new Ext.ux.Portal.DropZone(this, Ext.apply({ddGroup: 'portal'}, this.dropConfig));
    },
    
    getState: function() {
        var state = [];
        for (var i = 0; i < this.items.length; i++) {
            var col = this.items.items[i];
            state[i] = [];
            for (var j = 0; j < col.items.length; j++) {
                var p = col.items.items[j];
                state[i][j] = Ext.applyIf({xtype: p.getXType(), id: p.id}, p.initialConfig);
            }
        }
        return state;
    },

    applyState: function(state, config) {
        this.stateful = false;
        for (var i = 0; i < state.length; i++) {
            var col = this.items.items[i];
            while (col.items && col.items.length > 0) col.remove(col.items.items[0]);
            for (var j = 0; j < state[i].length; j++)
                col.add(state[i][j]);
        }
        this.stateful = true;
    },
    
    adjustForScrollbar: function() {
        if (this.disabled)
            this.on('enable', this.adjustForScrollbar, this);
        else if (this.hidden)
            this.on('show', this.adjustForScrollbar, this);
        else if (!this.rendered)
            this.on('render', this.adjustForScrollbar, this);
        else
        {
            var cw = this.body.dom.clientWidth;
            if(!this.lastCW){
                this.lastCW = cw;
            }else if(this.lastCW != cw){
                this.lastCW = cw;
                this.doLayout();
            }
            this.adjustForScrollbar.defer(100, this);
        }
    }
});
Ext.reg('portal', Ext.ux.Portal);


Ext.ux.Portal.DropZone = function(portal, cfg){
    this.portal = portal;
    Ext.dd.ScrollManager.register(portal.body);
    Ext.ux.Portal.DropZone.superclass.constructor.call(this, portal.bwrap.dom, cfg);
    portal.body.ddScrollConfig = this.ddScrollConfig;
};

Ext.extend(Ext.ux.Portal.DropZone, Ext.dd.DropTarget, {
    ddScrollConfig : {
        vthresh: 50,
        hthresh: -1,
        animate: true,
        increment: 200
    },

    createEvent : function(dd, e, data, col, c, pos){
        return {
            portal: this.portal,
            panel: data.panel,
            columnIndex: col,
            column: c,
            position: pos,
            data: data,
            source: dd,
            rawEvent: e,
            status: this.dropAllowed
        };
    },

    notifyOver : function(dd, e, data){
        var xy = e.getXY(), portal = this.portal, px = dd.proxy;

        // case column widths
        if(!this.grid){
            this.grid = this.getGrid();
        }

        // determine column
        var col = 0, xs = this.grid.columnX, cmatch = false;
        for(var len = xs.length; col < len; col++){
            if(xy[0] < (xs[col].x + xs[col].w)){
                cmatch = true;
                break;
            }
        }
        // no match, fix last index
        if(!cmatch){
            col--;
        }

        // find insert position
        var p, match = false, pos = 0,
            c = portal.items.itemAt(col),
            items = c.items.items;

        for(var len = items.length; pos < len; pos++){
            p = items[pos];
            var h = p.el.getHeight();
            if(h !== 0 && (p.el.getY()+(h/2)) > xy[1]){
                match = true;
                break;
            }
        }

        var overEvent = this.createEvent(dd, e, data, col, c,
                match && p ? pos : c.items.getCount());

        if(portal.fireEvent('validatedrop', overEvent) !== false &&
           portal.fireEvent('beforedragover', overEvent) !== false){

            if (!px.getProxy)
            {
                if (p)
                    px.proxy = p.el.insertSibling({cls:'x-panel-dd-spacer'});
                else
                    px.proxy = Ext.DomHelper.append(c.el.dom, {cls:'x-panel-dd-spacer'}, true);
                px.getProxy = function() { return this.proxy; };
                px.moveProxy = function(parentNode, before){
                    if(this.proxy){
                        parentNode.insertBefore(this.proxy.dom, before);
                    }
                }
            }

            // make sure proxy width is fluid
            px.getProxy().setWidth('auto');

            if(p){
                px.moveProxy(p.el.dom.parentNode, match ? p.el.dom : null);
            }else{
                px.moveProxy(c.el.dom, null);
            }

            this.lastPos = {c: c, col: col, p: match && p ? pos : false};
            this.scrollPos = portal.body.getScroll();

            portal.fireEvent('dragover', overEvent);

            return overEvent.status;;
        }else{
            return overEvent.status;
        }

    },

    notifyOut : function(dd, e, data){
        delete this.grid;
        dd.proxy.getProxy().remove();
    },

    notifyDrop : function(dd, e, data){
        delete this.grid;
        if(!this.lastPos){
            return false;
        }
        var c = this.lastPos.c, col = this.lastPos.col, pos = this.lastPos.p;
        delete this.lastPos;

        var dropEvent = this.createEvent(dd, e, data, col, c,
                pos !== false ? pos : c.items.getCount());

        if(this.portal.fireEvent('validatedrop', dropEvent) !== false &&
           this.portal.fireEvent('beforedrop', dropEvent) !== false){

            dd.proxy.getProxy().remove();
            
            if (!dd.panel) {
				if (data.node && data.node.attributes.portlet)
					dd.panel = data.node.attributes.portlet;
				if (data.node && data.node.attributes.panel)
					dd.panel = data.node.attributes.panel;
				else if (data.panel)
					dd.panel = data.panel;
				else
					return false;
			}
            if (dd.panel.el)
                dd.panel.el.dom.parentNode.removeChild(dd.panel.el.dom);
            if(pos !== false){
                c.insert(pos, dd.panel);
            }else{
                c.add(dd.panel);
            }
            
            this.portal.fireEvent('drop', dropEvent);

            // scroll position is lost on drop, fix it
            var st = this.scrollPos.top;
            if(st){
                var d = this.portal.body.dom;
                setTimeout(function(){
                    d.scrollTop = st;
                }, 10);
            }
            
            //The panel is hidden until after this function returns, but before afterDragDrop
            //defining afterDragDrop here allows the column to resize the panel (and its contents) to its width
            dd.afterDragDrop = function() {c.doLayout();}
            return true;
        }
        
        return false;
    },
    

    // internal cache of body and column coords
    getGrid : function(){
        var box = this.portal.bwrap.getBox();
        box.columnX = [];
        this.portal.items.each(function(c){
             box.columnX.push({x: c.el.getX(), w: c.el.getWidth()});
        });
        return box;
    }
});
