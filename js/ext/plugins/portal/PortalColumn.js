Ext.ux.PortalColumn = Ext.extend(Ext.Container, {
    layout: 'anchor',
    autoEl: 'div',
    defaultType: 'portlet',
    cls:'x-portal-column',
    initComponent : function() {
        Ext.ux.PortalColumn.superclass.initComponent.apply(this, arguments);
        this.on('remove', function(container, component) {
            Ext.state.Manager.clear(component.stateId || component.id);
            this.ownerCt.saveState.defer(100, this.ownerCt);
        });
        this.on('add', function() {
            this.ownerCt.saveState.defer(100, this.ownerCt);
        });
    }
});
Ext.reg('portalcolumn', Ext.ux.PortalColumn);