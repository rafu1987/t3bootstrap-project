/***************************************************************
*  Copyright notice
*
*  (c) 2012 Markus Blaschke (TEQneers GmbH & Co. KG) <blaschke@teqneers.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 3 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

Ext.ns('TQSeo.overview');

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

	TQSeo.overview.grid.init();
});

TQSeo.overview.grid = {

	_cellEditMode: false,
	_fullCellHighlight: true,

	gridDs: false,
	grid: false,

	filterReload: function() {
		TQSeo.overview.conf.criteriaFulltext = Ext.getCmp('searchFulltext').getValue();
		TQSeo.overview.conf.depth = Ext.getCmp('listDepth').getValue();

		this.grid.getView().refresh();
	},

	storeReload: function() {
		TQSeo.overview.conf.criteriaFulltext = Ext.getCmp('searchFulltext').getValue();
		TQSeo.overview.conf.depth = Ext.getCmp('listDepth').getValue();

		this.gridDs.reload();
	},

	init: function() {
		// Init
		var me = this;

		/****************************************************
		 * settings
		 ****************************************************/
		switch( TQSeo.overview.conf.listType ) {
			case 'metadata':
				this._cellEditMode = true;
				break;

			case 'searchengines':
				this._fullCellHighlight = false;
				this._cellEditMode = true;
				break;

			case 'url':
				this._fullCellHighlight = false;
				this._cellEditMode = true;
				break;

			case 'pagetitle':
				this._cellEditMode = true;
				this._fullCellHighlight = false;
				break;

			case 'pagetitlesim':
				this._fullCellHighlight = false;
				break;

			default:
				// Not defined
				return;
				break;
		}

		/****************************************************
		 * grid storage
		 ****************************************************/
		this.gridDs = this._createGridDs();

		/****************************************************
		 * column model
		 ****************************************************/
		var columnModel = this._createGridColumnModel();

		/****************************************************
		 * grid panel
		 ****************************************************/
		var grid = new Ext.grid.GridPanel({
			layout: 'fit',
			renderTo: TQSeo.overview.conf.renderTo,
			store: this.gridDs,
			loadMask: true,
			plugins: [new Ext.ux.plugin.FitToParent()],
			columns: columnModel,
			stripeRows: true,
			height: 350,
			width: '98%',
			frame: true,
			border: true,
			disableSelection: false,
			title: TQSeo.overview.conf.lang.title,
			viewConfig: {
				forceFit: true,
				listeners: {
					refresh: function(view) {
						if (!this._fullCellHighlight && !Ext.isEmpty(TQSeo.overview.conf.criteriaFulltext)) {
							view.el.select('.x-grid3-body .x-grid3-cell').each(function(el) {
								TQSeo.highlightText(el.dom, TQSeo.overview.conf.criteriaFulltext);
							});
						}
					}
				}
			},
			tbar: [
				TQSeo.overview.conf.lang.labelSearchFulltext,
				{
					xtype: 'textfield',
					id: 'searchFulltext',
					fieldLabel: TQSeo.overview.conf.lang.labelSearchFulltext,
					emptyText : TQSeo.overview.conf.lang.emptySearchFulltext,
					listeners: {
						specialkey: function(f,e){
							if (e.getKey() == e.ENTER) {
								me.filterReload(this);
							}
						}
					}
				},
				{xtype: 'tbspacer', width: 10}
			],
			bbar: [
				TQSeo.overview.conf.lang.labelDepth,
		    	{
		    		xtype: 'combo',
		    		id: 'listDepth',
					listeners: {
						select: function(f,e){
							me.storeReload(this);
						}
					},
					forceSelection: true,
					editable: false,
					mode: 'local',
					triggerAction: 'all',
					value : TQSeo.overview.conf.depth,
					store: new Ext.data.ArrayStore({
						id: 0,
						fields: [
							'id',
							'label'
						],
						data: [
							[1, 1],
							[2, 2],
							[3, 3],
							[4, 4],
							[5, 5]
						]
					}),
					valueField: 'id',
					displayField: 'label'
		    	}
			]
		});
		this.grid = grid;

		var editWindow = false;

		if( this._cellEditMode ) {
			grid.addClass('tqseo-grid-editable');

			grid.on('cellclick', function(grid, rowIndex, colIndex, e) {
				var record = grid.getStore().getAt(rowIndex);
				var fieldName = grid.getColumnModel().getDataIndex(colIndex);
				var fieldId = grid.getColumnModel().getColumnId(colIndex);
				var col = grid.getColumnModel().getColumnById(fieldId);
				var data = record.get(fieldName);

				var title = record.get('title');

				if( col.tqSeoOnClick ) {
					col.tqSeoOnClick(record, fieldName, fieldId, col, data);
				}

				if( col.tqSeoEditor ) {
					// Init editor field
					var field = col.tqSeoEditor;
					field.itemId = 'form-field';

					if( !field.width)	field.width = 375;

					switch( field.xtype ) {
						case 'textarea':
							if( !field.height)	field.height = 150;
							field.value = data;
							break;

						case 'checkbox':
							if( data == '0' || data == '' ) {
								field.checked = false;
							} else {
								field.checked = true;
							}
							break;

						default:
							field.value = data;
							break;
					}

					// Init editor window
					var editWindow = new Ext.Window({
						xtype: 'form',
						width: 400,
						height: 'auto',
						modal: true,
						title: title+': '+col.header,
						items: [ field ],
						buttons: [
							{
								text: 'Save',
								itemId: 'form-button-save',
								disabled: true,
								handler: function(cmp, e) {
									grid.loadMask.show();

									var pid = record.get('uid');
									var fieldValue = editWindow.getComponent('form-field').getValue();

									var callbackFinish = function(response) {
										var response = Ext.decode(response.responseText);

										if( response && response.error ) {
											TYPO3.Flashmessage.display(TYPO3.Severity.error, '', Ext.util.Format.htmlEncode(response.error) );
										}

										grid.getStore().load();
									};

									Ext.Ajax.request({
										url: TQSeo.overview.conf.ajaxController + '&cmd=updatePageField',
										params: {
											pid				: Ext.encode(pid),
											field			: Ext.encode(fieldName),
											value			: Ext.encode(fieldValue),
											sessionToken	: Ext.encode( TQSeo.overview.conf.sessionToken )
										},
										success: callbackFinish,
										failure: callbackFinish
									});

									editWindow.destroy();
								}
							},{
								text: 'Cancel',
								handler: function(cmp, e) {
									editWindow.destroy();
								}
							}
						]
					});
					editWindow.show();

					var formField		= editWindow.getComponent('form-field');
					var formButtonSave	= editWindow.getFooterToolbar().getComponent('form-button-save');

					// add listeners
					formField.on('valid', function() {
						formButtonSave.setDisabled(false);
					});

					formField.on('invalid', function() {
						formButtonSave.setDisabled(true);
					});


				}
			});
		}

	},


	_createGridDs: function() {
		var me = this;

		var gridDsColumns = [
			{name: 'uid', type: 'int'},
			{name: 'title', type: 'string'},
			{name: '_depth', type: 'int'}
		];

		switch( TQSeo.overview.conf.listType ) {
			case 'metadata':
				gridDsColumns.push(
					{name: 'keywords', type: 'string'},
					{name: 'description', type: 'string'},
					{name: 'abstract', type: 'string'},
					{name: 'author', type: 'string'},
					{name: 'author_email', type: 'string'},
					{name: 'lastupdated', type: 'string'}
				);
				break;

			case 'searchengines':
				gridDsColumns.push(
					{name: 'tx_tqseo_canonicalurl', type: 'string'},
					{name: 'tx_tqseo_is_exclude', type: 'string'},
					{name: 'tx_tqseo_priority', type: 'string'}
				);
				break;

			case 'url':
				gridDsColumns.push(
					{name: 'alias', type: 'string'},
					{name: 'url_scheme', type: 'string'}
				);

				if( TQSeo.overview.conf.realurlAvailable ) {
					gridDsColumns.push(
						{name: 'tx_realurl_pathsegment', type: 'string'},
						{name: 'tx_realurl_pathoverride', type: 'string'},
						{name: 'tx_realurl_exclude', type: 'string'}
					);
				}
				break;

			case 'pagetitle':
				gridDsColumns.push(
					{name: 'tx_tqseo_pagetitle', type: 'string'},
					{name: 'tx_tqseo_pagetitle_rel', type: 'string'},
					{name: 'tx_tqseo_pagetitle_prefix', type: 'string'},
					{name: 'tx_tqseo_pagetitle_suffix', type: 'string'}
				);
				break;

			case 'pagetitlesim':
				gridDsColumns.push(
					{name: 'title_simulated', type: 'string'}
				);
				break;
		}

		var gridDs = new Ext.data.Store({
			storeId: 'TQSeoOverviewRecordsStore',
			autoLoad: true,
			remoteSort: true,
			url: TQSeo.overview.conf.ajaxController + '&cmd=getList',
			reader: new Ext.data.JsonReader({
					totalProperty: 'results',
					root: 'rows'
				},
				gridDsColumns
			),
			sortInfo: {
				field	 : 'uid',
				direction: 'DESC'
			},
			baseParams: {
				pid						: Ext.encode( TQSeo.overview.conf.pid ),
				pagerStart				: Ext.encode( 0 ),
				pagingSize				: Ext.encode( TQSeo.overview.conf.pagingSize ),
				sortField				: Ext.encode( TQSeo.overview.conf.sortField ),
				depth					: Ext.encode( TQSeo.overview.conf.depth ),
				listType				: Ext.encode( TQSeo.overview.conf.listType ),
				sessionToken			: Ext.encode( TQSeo.overview.conf.sessionToken )
			},
			listeners: {
				beforeload: function() {
					this.baseParams.pagingSize	= Ext.encode( TQSeo.overview.conf.pagingSize );
					this.baseParams.depth		= Ext.encode( TQSeo.overview.conf.depth );
					this.removeAll();
				}
			}
		});

		return gridDs;
	},


	_createGridColumnModel: function() {
		var me = this;

		var fieldRenderer = function(value, metaData, record, rowIndex, colIndex, store) {
			return me._fieldRenderer(value);
		};

		var fieldRendererRaw = function(value, metaData, record, rowIndex, colIndex, store) {
			return me._fieldRendererRaw(value);
		};

		var fieldRendererBoolean = function(value, metaData, record, rowIndex, colIndex, store) {
			if( value == 0 || value == '' ) {
				value = '<div class="tqseo-boolean">'+Ext.util.Format.htmlEncode(TQSeo.overview.conf.lang.boolean_no)+'</div>';
			} else {
				value = '<div class="tqseo-boolean"><strong>'+Ext.util.Format.htmlEncode(TQSeo.overview.conf.lang.boolean_yes)+'</strong></div>';
			}

			return me._fieldRendererCallback(value, '', false, false);
		}

		var columnModel = [{
			id       : 'uid',
			header   : TQSeo.overview.conf.lang.page_uid,
			width    : 'auto',
			sortable : false,
			dataIndex: 'uid',
			hidden	 : true
		}, {
			id       : 'title',
			header   : TQSeo.overview.conf.lang.page_title,
			width    : 200,
			sortable : false,
			dataIndex: 'title',
			renderer: function(value, metaData, record, rowIndex, colIndex, store) {
				var qtip = value;

				if( record.data._depth ) {
					value = new Array(record.data._depth).join('    ') + value;
				}

				return me._fieldRendererCallback(value, qtip, false, true);
			},
			tqSeoEditor	: {
				xtype: 'textfield',
				minLength: 3
			}
		}];

		switch( TQSeo.overview.conf.listType ) {
			case 'metadata':
				columnModel.push({
					id			: 'keywords',
					header		: TQSeo.overview.conf.lang.page_keywords,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'keywords',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textarea'
					}
				},{
					id			: 'description',
					header		: TQSeo.overview.conf.lang.page_description,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'description',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textarea'
					}
				},{
					id			: 'abstract',
					header		: TQSeo.overview.conf.lang.page_abstract,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'abstract',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textarea'
					}
				},{
					id			: 'author',
					header		: TQSeo.overview.conf.lang.page_author,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'author',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id			: 'author_email',
					header		: TQSeo.overview.conf.lang.page_author_email,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'author_email',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield',
						vtype: 'email'
					}
				},{
					id			: 'lastupdated',
					header		: TQSeo.overview.conf.lang.page_lastupdated,
					width		: 'auto',
					sortable	: false,
					dataIndex	: 'lastupdated',
					renderer	: fieldRendererRaw,
					tqSeoEditor	: {
						xtype: 'datefield',
						format: 'Y-m-d'
					}
				});

				break;

			case 'searchengines':

				var fieldRendererSitemapPriority = function(value, metaData, record, rowIndex, colIndex, store) {
					var qtip = value;

					if( value == '0' ) {
						value = '<span class="tqseo-default">'+Ext.util.Format.htmlEncode(TQSeo.overview.conf.lang.value_default)+'</span>';
					} else {
						value = Ext.util.Format.htmlEncode(value);
					}

					return me._fieldRendererCallback(value, qtip, false, false);
				}

				columnModel.push({
					id       : 'tx_tqseo_canonicalurl',
					header   : TQSeo.overview.conf.lang.page_searchengine_canonicalurl,
					width    : 400,
					sortable : false,
					dataIndex: 'tx_tqseo_canonicalurl',
					renderer : fieldRendererRaw,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id       : 'tx_tqseo_priority',
					header   : TQSeo.overview.conf.lang.page_sitemap_priority,
					width    : 150,
					sortable : false,
					dataIndex: 'tx_tqseo_priority',
					renderer : fieldRendererSitemapPriority,
					tqSeoEditor	: {
						xtype: 'numberfield'
					}
				},{
					id       : 'tx_tqseo_is_exclude',
					header   : TQSeo.overview.conf.lang.page_searchengine_is_exclude,
					width    : 100,
					sortable : false,
					dataIndex: 'tx_tqseo_is_exclude',
					renderer : fieldRendererBoolean,
					tqSeoEditor	: {
						xtype: 'combo',
						forceSelection: true,
						editable: false,
						mode: 'local',
						triggerAction: 'all',
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: [
								'id',
								'label'
							],
							data: [
								[0, TQSeo.overview.conf.lang.searchengine_is_exclude_disabled],
								[1, TQSeo.overview.conf.lang.searchengine_is_exclude_enabled]
							]
						}),
						valueField: 'id',
						displayField: 'label'
					}
				});
				break;


			case 'url':
				var fieldRendererUrlScheme = function(value, metaData, record, rowIndex, colIndex, store) {
					var ret = '';

					value = parseInt(value);
					switch(value) {
						case 0:
							ret = '<span class="tqseo-default">'+Ext.util.Format.htmlEncode( TQSeo.overview.conf.lang.page_url_scheme_default )+'</span>';
							break;

						case 1:
							ret = '<strong class="tqseo-url-http">'+Ext.util.Format.htmlEncode( TQSeo.overview.conf.lang.page_url_scheme_http)+'</strong>';
							break;

						case 2:
							ret = '<strong class="tqseo-url-https">'+Ext.util.Format.htmlEncode( TQSeo.overview.conf.lang.page_url_scheme_https)+'</strong>';
							break;
					}

					return ret;
				}

				var fieldRendererUrlSimulate = function(value, metaData, record, rowIndex, colIndex, store) {
					var qtip = Ext.util.Format.htmlEncode(TQSeo.overview.conf.lang.qtip_url_simulate);

					return '<div class="tqseo-toolbar" ext:qtip="' + qtip +'">'+TQSeo.overview.conf.sprite.info+'</div>';
				}


				columnModel.push({
					id       : 'url_scheme',
					header   : TQSeo.overview.conf.lang.page_url_scheme,
					width    : 100,
					sortable : false,
					dataIndex: 'url_scheme',
					renderer : fieldRendererUrlScheme,
					tqSeoEditor	: {
						xtype: 'combo',
						forceSelection: true,
						editable: false,
						mode: 'local',
						triggerAction: 'all',
						store: new Ext.data.ArrayStore({
							id: 0,
							fields: [
								'id',
								'label'
							],
							data: [
								[0, TQSeo.overview.conf.lang.page_url_scheme_default],
								[1, TQSeo.overview.conf.lang.page_url_scheme_http],
								[2, TQSeo.overview.conf.lang.page_url_scheme_https]
							]
						}),
						valueField: 'id',
						displayField: 'label'
					}
				},{
					id       : 'alias',
					header   : TQSeo.overview.conf.lang.page_url_alias,
					width    : 200,
					sortable : false,
					dataIndex: 'alias',
					renderer : fieldRendererRaw,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				});

				if( TQSeo.overview.conf.realurlAvailable ) {
					columnModel.push({
						id       : 'tx_realurl_pathsegment',
						header   : TQSeo.overview.conf.lang.page_url_realurl_pathsegment,
						width    : 200,
						sortable : false,
						dataIndex: 'tx_realurl_pathsegment',
						renderer : fieldRendererRaw,
						tqSeoEditor	: {
							xtype: 'textfield'
						}
					},{
						id       : 'tx_realurl_pathoverride',
						header   : TQSeo.overview.conf.lang.page_url_realurl_pathoverride,
						width    : 150,
						sortable : false,
						dataIndex: 'tx_realurl_pathoverride',
						renderer : fieldRendererBoolean,
						tqSeoEditor	: {
							xtype: 'combo',
							forceSelection: true,
							editable: false,
							mode: 'local',
							triggerAction: 'all',
							store: new Ext.data.ArrayStore({
								id: 0,
								fields: [
									'id',
									'label'
								],
								data: [
									[0, TQSeo.overview.conf.lang.boolean_no],
									[1, TQSeo.overview.conf.lang.boolean_yes]
								]
							}),
							valueField: 'id',
							displayField: 'label'
						}
					},{
						id       : 'tx_realurl_exclude',
						header   : TQSeo.overview.conf.lang.page_url_realurl_exclude,
						width    : 150,
						sortable : false,
						dataIndex: 'tx_realurl_exclude',
						renderer : fieldRendererBoolean,
						tqSeoEditor	: {
							xtype: 'combo',
							forceSelection: true,
							editable: false,
							mode: 'local',
							triggerAction: 'all',
							store: new Ext.data.ArrayStore({
								id: 0,
								fields: [
									'id',
									'label'
								],
								data: [
									[0, TQSeo.overview.conf.lang.boolean_no],
									[1, TQSeo.overview.conf.lang.boolean_yes]
								]
							}),
							valueField: 'id',
							displayField: 'label'
						}
					},{
						id       : 'url_simulated',
						header   : '',
						width    : 50,
						sortable : false,
						renderer : fieldRendererUrlSimulate,
						tqSeoOnClick: function(record, fieldName, fieldId, col, data) {
							me.grid.loadMask.show();

							var callbackFinish = function(response) {
								var response = Ext.decode(response.responseText);

								me.grid.loadMask.hide();

								if( response && response.error ) {
									TYPO3.Flashmessage.display(TYPO3.Severity.error, '', Ext.util.Format.htmlEncode(response.error) );
								}

								if( response && response.url ) {
									TYPO3.Flashmessage.display(TYPO3.Severity.information, '', Ext.util.Format.htmlEncode(response.url) );
								}
							};

							Ext.Ajax.request({
								url: TQSeo.overview.conf.ajaxController + '&cmd=generateSimulatedUrl',
								params: {
									pid				: Ext.encode(record.get('uid')),
									sessionToken	: Ext.encode( TQSeo.overview.conf.sessionToken )
								},
								success: callbackFinish,
								failure: callbackFinish
							});

						}
					});
				}

				break;


			case 'pagetitle':
				var fieldRendererTitleSimulate = function(value, metaData, record, rowIndex, colIndex, store) {
					var qtip = Ext.util.Format.htmlEncode(TQSeo.overview.conf.lang.qtip_pagetitle_simulate);

					return '<div class="tqseo-toolbar" ext:qtip="' + qtip +'">'+TQSeo.overview.conf.sprite.info+'</div>';
				}

				columnModel.push({
					id       : 'tx_tqseo_pagetitle_rel',
					header   : TQSeo.overview.conf.lang.page_tx_tqseo_pagetitle_rel,
					width    : 'auto',
					sortable : false,
					dataIndex: 'tx_tqseo_pagetitle_rel',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id       : 'tx_tqseo_pagetitle_prefix',
					header   : TQSeo.overview.conf.lang.page_tx_tqseo_pagetitle_prefix,
					width    : 'auto',
					sortable : false,
					dataIndex: 'tx_tqseo_pagetitle_prefix',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id       : 'tx_tqseo_pagetitle_suffix',
					header   : TQSeo.overview.conf.lang.page_tx_tqseo_pagetitle_suffix,
					width    : 'auto',
					sortable : false,
					dataIndex: 'tx_tqseo_pagetitle_suffix',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id       : 'tx_tqseo_pagetitle',
					header   : TQSeo.overview.conf.lang.page_tx_tqseo_pagetitle,
					width    : 'auto',
					sortable : false,
					dataIndex: 'tx_tqseo_pagetitle',
					renderer	: fieldRenderer,
					tqSeoEditor	: {
						xtype: 'textfield'
					}
				},{
					id       : 'title_simulated',
					header   : '',
					width    : 50,
					sortable : false,
					renderer : fieldRendererTitleSimulate,
					tqSeoOnClick: function(record, fieldName, fieldId, col, data) {
						me.grid.loadMask.show();

						var callbackFinish = function(response) {
							var response = Ext.decode(response.responseText);

							me.grid.loadMask.hide();

							if( response && response.error ) {
								TYPO3.Flashmessage.display(TYPO3.Severity.error, '', Ext.util.Format.htmlEncode(response.error) );
							}

							if( response && response.title ) {
								TYPO3.Flashmessage.display(TYPO3.Severity.information, '', Ext.util.Format.htmlEncode(response.title) );
							}
						};

						Ext.Ajax.request({
							url: TQSeo.overview.conf.ajaxController + '&cmd=generateSimulatedTitle',
							params: {
								pid				: Ext.encode(record.get('uid')),
								sessionToken	: Ext.encode( TQSeo.overview.conf.sessionToken )
							},
							success: callbackFinish,
							failure: callbackFinish
						});

					}
				});
				break;

			case 'pagetitlesim':
				columnModel.push({
					id       : 'title_simulated',
					header   : TQSeo.overview.conf.lang.page_title_simulated,
					width    : 400,
					sortable : false,
					dataIndex: 'title_simulated',
					renderer : fieldRendererRaw
				});
				break;

		}


		// Add tooltip
		Ext.each(columnModel, function(item, index) {
			if( !item.tooltip ) {
				item.tooltip = item.header;
			}
		});

		return columnModel;
	},


	_fieldRenderer: function(value) {
		return this._fieldRendererCallback(value, value, 23, true);
	},

	_fieldRendererRaw: function(value) {
		return this._fieldRendererCallback(value, value, false, true);
	},

	_fieldRendererCallback: function(value, qtip, maxLength, escape) {
		var classes = '';
		var icon = '';

		if( this._cellEditMode ) {
			classes += 'tqseo-cell-editable ';
			icon = TQSeo.overview.conf.sprite.edit;
		}

		if(this._fullCellHighlight && !Ext.isEmpty(TQSeo.overview.conf.criteriaFulltext)) {
			if( TQSeo.highlightTextExists(value, TQSeo.overview.conf.criteriaFulltext) ) {
				classes += 'tqseo-cell-highlight ';
			}
		}

		if( maxLength && value != '' && value.length >= maxLength ) {
			value = value.substring(0, (maxLength-3) )+'...';
		}
		
		if(escape) {
			value = Ext.util.Format.htmlEncode(value);
			value = value.replace(/ /g, "&nbsp;");
			value += '&nbsp;';
		}



		if(escape) {
			qtip = Ext.util.Format.htmlEncode(qtip);
		}
		qtip = qtip.replace(/\n/g, "<br />");

		return '<div class="'+classes+'" ext:qtip="' + qtip +'">' + value +icon+'</div>';
	}


};