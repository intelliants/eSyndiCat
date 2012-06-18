/**
 * Auxiliary class for building grid 
 * @class This is the grid auxiliary class.  
 * @constructor 
 */
intelli.gmodel = function(conf)
{
	var url = conf.url || 'data.php';

	/**
	 * Check column object
	 */
	this.checkColumn = new Ext.grid.CheckboxSelectionModel();

	this.record = null;
	this.reader = null;
	this.proxy = null;
	
	this.columnModel = null;

	this.dataStore = null;

	/** 
	 * Initialization proxy object
	 *
	 * @return {Object}
	 */
	this.setupProxy = function()
	{
		return new Ext.data.HttpProxy({url: url, method: 'GET'});
	};

	/**
	 * Initialization data store object
	 *
	 * @return {Object}
	 */
	this.setupDataStore = function()
	{
		this.proxy = this.setupProxy();
		this.reader = this.setupReader();

		this.dataStore = new Ext.data.Store({
			remoteSort: true,
			proxy: this.proxy,
			reader: this.reader
		});

		return this.dataStore;
	};

	/**
	 * Initialization selection model 
	 *
	 * @return {Object}
	 */
	this.setupSelectionModel = function()
	{
		var selectionModel = new Ext.grid.CheckboxSelectionModel();

		return selectionModel;
	};
};
