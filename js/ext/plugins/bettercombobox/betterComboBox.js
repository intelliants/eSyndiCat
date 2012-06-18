/**
 * @class Ext.ux.BetterComboBox
 * @extends Ext.form.ComboBox
 * Fires "change" event at time of actual change, instead of just before/during blur
 * @author mcurrey
 */
Ext.ux.BetterComboBox = Ext.extend(Ext.form.ComboBox, {

    initComponent : function(){
        Ext.ux.BetterComboBox.superclass.initComponent.call(this);
    },

    initEvents : function(){
        Ext.ux.BetterComboBox.superclass.initEvents.call(this);
        this.on('select', this.onBetterSelect, this);
    },

    // named this way to not interfere with Ext.form.ComboBox.onSelect()
    onBetterSelect : function(record, index){
        var v = this.getValue();
        if(String(v) !== String(this.startValue)){
            this.fireEvent('change', this, v, this.startValue);
			this.triggerBlur.defer(50, this);
        }
    }

});

Ext.reg('bettercombo', Ext.ux.BetterComboBox);
