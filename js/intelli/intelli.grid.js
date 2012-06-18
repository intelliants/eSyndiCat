/**
 * Class a new Grid object.
 * @class This is the basic Grid class.  
 * It can be considered an abstract class, even though no such thing
 * really existing in JavaScript
 * @constructor
 */
intelli.grid = function(config)
{
	/** 
	 * The object of grid 
	 */
	this.grid;
	
	/** 
	 * The object of datastore.
	 */
	this.dataStore;

	/** 
	 * The object of column model. 
	 */
	this.columnModel;
	
	/** 
	 * The object of selection model. 
	 */
	this.selectionModel;
	
	/** 
	 * The object of tool bar. 
	 */
	this.topToolbar;
	
	/**
     * The object of paging tool bar.
     */
	this.bottomToolbar;

	this.plugins = config.plugins || '';

	this.start = config.start || 0;
	this.limit = config.limit || 20;

	this.title = config.title || '';
	this.renderTo = config.renderTo || '';

	/** 
	 * Installing the grid object 
	 */
	this.setupGrid = function()
	{
		this.grid = new Ext.grid.EditorGridPanel({
			store: this.dataStore,
			colModel: this.columnModel,
			selModel: this.selectionModel,
			autoWidth: true,
			height: 480,
			title: this.title,
			renderTo: this.renderTo,
			frame: true,
			loadMask: true,
			stateful: true,
			plugins: this.plugins,
			trackMouseOver: true,
			bbar: this.bottomToolbar,
			tbar: this.topToolbar
		});
	};

	this.getGrid = function()
	{
		return this.grid;
	};

	/**
	 * Load data
	 *
	 */
	this.loadData = function()
	{
		var grid = this.getGrid();
		var name = 'startStore_' + grid.id;
		var state = Ext.state.Manager.getProvider();
		var stateStart = state.get(name, 0);

		var bbar = grid.getBottomToolbar();

		if('undefined' != typeof bbar)
		{
			var pageStore = 'pageSizeStore_' + grid.id;
			var pageRestore = state.get(pageStore, 0);

			if(pageRestore)
			{
				bbar.pageSize = pageRestore;
				Ext.getCmp('pgnPnl').setValue(pageRestore);

				state.clear(pageStore);
			}
		}
		
		var start = stateStart ? stateStart : this.start;
		var limit = pageRestore ? pageRestore : this.limit;

		state.clear(name);

		this.dataStore.load({params:{start: start, limit: limit}});
	};

	this.saveGridState = function()
	{
		var grid = this.getGrid();
		var state = Ext.state.Manager.getProvider();
		var name = 'startStore_' + grid.id;
		var bbar = grid.getBottomToolbar();

		if('undefined' != typeof bbar)
		{
			var pageStore = 'pageSizeStore_' + grid.id;

			state.set(pageStore, bbar.pageSize);
		}
		
		state.set(name, grid.store.lastOptions.params.start);
	}
};
