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

Ext.ns('TQSeo.sitemap');

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

	TQSeo.sitemap.grid.init();
});

TQSeo.sitemap.grid = {

	init: function() {
		/****************************************************
		 * grid storage
		 ****************************************************/
		var gridDs = new Ext.data.Store({
	 		storeId: 'TQSeoSitemapRecordsStore',
			autoLoad: true,
			remoteSort: true,
			url: TQSeo.sitemap.conf.ajaxController + '&cmd=getList',
			reader: new Ext.data.JsonReader({
					totalProperty: 'results',
					root: 'rows'
				},[
					{name: 'uid', type: 'int'},
					{name: 'page_rootpid', type: 'int'},
					{name: 'page_uid', type: 'int'},
					{name: 'page_url', type: 'string' },
					{name: 'page_depth', type: 'int' },
					{name: 'page_language', type: 'int' },
					{name: 'page_change_frequency', type: 'int' },
					{name: 'tstamp', type: 'string' },
					{name: 'crdate', type: 'string' }
				]
			),
			sortInfo: {
				field	 : 'uid',
				direction: 'DESC'
			},
			baseParams: {
				pid						: Ext.encode( TQSeo.sitemap.conf.pid ),
				pagerStart				: 0,
				pagingSize				: Ext.encode( TQSeo.sitemap.conf.pagingSize ),
				sort					: TQSeo.sitemap.conf.sortField,
				dir						: TQSeo.sitemap.conf.sortDir,
				criteriaFulltext		: Ext.encode( TQSeo.sitemap.conf.criteriaFulltext ),
				criteriaPageUid			: Ext.encode( TQSeo.sitemap.conf.criteriaPageUid ),
				criteriaPageLanguage	: Ext.encode( TQSeo.sitemap.conf.criteriaPageLanguage ),
				criteriaPageDepth		: Ext.encode( TQSeo.sitemap.conf.criteriaPageDepth ),
				sessionToken			: Ext.encode( TQSeo.sitemap.conf.sessionToken )
			},
			listeners: {
				beforeload: function() {
					this.baseParams.pagingSize				= Ext.encode( TQSeo.sitemap.conf.pagingSize );
					this.baseParams.criteriaFulltext		= Ext.encode( TQSeo.sitemap.conf.criteriaFulltext );
					this.baseParams.criteriaPageUid			= Ext.encode( TQSeo.sitemap.conf.criteriaPageUid );
					this.baseParams.criteriaPageLanguage	= Ext.encode( TQSeo.sitemap.conf.criteriaPageLanguage );
					this.baseParams.criteriaPageDepth		= Ext.encode( TQSeo.sitemap.conf.criteriaPageDepth );
					this.removeAll();
				}
			}
		});

 		var function_filter = function(ob) {
			filterAction(ob, 'getItems');
		};

 		var filterAction = function(ob, cmd) {
 			TQSeo.sitemap.conf.criteriaFulltext		= Ext.getCmp('searchFulltext').getValue();
			TQSeo.sitemap.conf.criteriaPageUid		= Ext.getCmp('searchPageUid').getValue();
			TQSeo.sitemap.conf.criteriaPageLanguage	= Ext.getCmp('searchPageLanguage').getValue();
			TQSeo.sitemap.conf.criteriaPageDepth	= Ext.getCmp('searchPageDepth').getValue();

 			gridDs.reload();
		};

		var function_delete = function(ob) {
			rowAction(ob, "delete", TQSeo.sitemap.conf.lang.messageDeleteTitle, TQSeo.sitemap.conf.lang.messageDeleteQuestion )
		}

		var rowAction = function(ob, cmd, confirmTitle, confirmText) {
			var recList = grid.getSelectionModel().getSelections();

			if( recList.length >= 1 ) {
				var uidList = [];
				for (i = 0; i < recList.length; i++) {
					uidList.push( recList[i].json.uid );
				}
				var frmConfirm = new Ext.Window({
					xtype: 'form',
					width: 200,
					height: 'auto',
					modal: true,
					title: confirmTitle,
					items: [
						{
							xtype: 'label',
							text: confirmText
						}
					],
					buttons: [
						{
							text: TQSeo.sitemap.conf.lang.buttonYes,
							handler: function(cmp, e) {
								Ext.Ajax.request({
									url: TQSeo.sitemap.conf.ajaxController + '&cmd=' + cmd,
									callback: function(options, success, response) {
										if (response.responseText === 'true') {
											// reload the records and the table selector
											gridDs.reload();
										} else {
											alert('ERROR: ' + response.responseText);
										}
									},
									params: {
										'uidList'		: Ext.encode(uidList),
										'pid'			: TQSeo.sitemap.conf.pid,
										sessionToken	: Ext.encode( TQSeo.sitemap.conf.sessionToken )
									}
								});

								frmConfirm.destroy();
							}
						},{
							text: TQSeo.sitemap.conf.lang.buttonNo,
							handler: function(cmp, e) {
								frmConfirm.destroy();
							}
						}
					]
				});
				frmConfirm.show();

			} else {
				// no row selected
				Ext.MessageBox.show({
					title: confirmTitle,
					msg: TQSeo.sitemap.conf.lang.errorNoSelectedItemsBody,
					buttons: Ext.MessageBox.OK,
					minWidth: 300,
					minHeight: 200,
					icon: Ext.MessageBox.INFO
				});
			}
		}

 		/****************************************************
		 * row checkbox
		 ****************************************************/
	 	var sm = new Ext.grid.CheckboxSelectionModel({
			singleSelect: false
		});


 		/****************************************************
		 * Renderer
		 ****************************************************/
		var dateToday		= new Date().format("Y-m-d");
		var dateYesterday	= new Date().add(Date.DAY, -1).format("Y-m-d");

		var rendererDatetime = function(value, metaData, record, rowIndex, colIndex, store) {
			var ret = Ext.util.Format.htmlEncode(value);
			var qtip = Ext.util.Format.htmlEncode(value);

			ret = ret.split(dateToday).join('<strong>'+TQSeo.sitemap.conf.lang.today+'</strong>');
			ret = ret.split(dateYesterday).join('<strong>'+TQSeo.sitemap.conf.lang.yesterday+'</strong>');

			return '<div ext:qtip="' + qtip +'">' + ret + '</div>';
		}


		var rendererLanguage = function(value, metaData, record, rowIndex, colIndex, store) {
			var ret = '';
			var qtip = '';

			if( TQSeo.sitemap.conf.languageFullList[value] ) {
				var lang = TQSeo.sitemap.conf.languageFullList[value];

				// Flag (if available)
				if( lang.flag ) {
					ret += '<span class="t3-icon t3-icon-flags t3-icon-flags-'+Ext.util.Format.htmlEncode(lang.flag)+' t3-icon-'+Ext.util.Format.htmlEncode(lang.flag)+'"></span>';
					ret += '&nbsp;';
				}

				// Label
				ret += Ext.util.Format.htmlEncode(lang.label);
				qtip = Ext.util.Format.htmlEncode(lang.label);

			} else {
				ret = value;
			}

			return '<div ext:qtip="' + qtip +'">' + ret + '</div>';
		}


		var rendererUrl = function(value, metaData, record, rowIndex, colIndex, store) {
			value = Ext.util.Format.htmlEncode(value);

			var qtip = Ext.util.Format.htmlEncode(value);

			return '<div ext:qtip="' + qtip +'">' + value + '</div>';
		}

		/****************************************************
		 * grid panel
		 ****************************************************/
		var grid = new Ext.grid.GridPanel({
			layout: 'fit',
			renderTo: TQSeo.sitemap.conf.renderTo,
			store: gridDs,
			loadMask: true,
			plugins: [new Ext.ux.plugin.FitToParent()],
			columns: [
				sm,
				{
					id       : 'page_uid',
					header   : TQSeo.sitemap.conf.lang.sitemap_page_uid,
					width    : 10,
					sortable : true,
					dataIndex: 'page_uid',
					css      : 'text-align: right;'
				},
				{
					id       : 'page_url',
					header   : TQSeo.sitemap.conf.lang.sitemap_page_url,
					width    : 'auto',
					sortable : true,
					dataIndex: 'page_url',
					renderer : rendererUrl
				},{
					id       : 'page_depth',
					header   : TQSeo.sitemap.conf.lang.sitemap_page_depth,
					width    : 10,
					sortable : true,
					dataIndex: 'page_depth',
					css      : 'text-align: right;padding-right: 10px;'

				},{
					id       : 'page_language',
					header   : TQSeo.sitemap.conf.lang.sitemap_page_language,
					width    : 15,
					sortable : true,
					dataIndex: 'page_language',
					renderer : rendererLanguage
				},{
					id       : 'crdate',
					header   : TQSeo.sitemap.conf.lang.sitemap_crdate,
					width    : 25,
					sortable : true,
					dataIndex: 'crdate',
					hidden   : true,
					renderer : rendererDatetime
				},{
					id       : 'tstamp',
					header   : TQSeo.sitemap.conf.lang.sitemap_tstamp,
					width    : 25,
					sortable : true,
					dataIndex: 'tstamp',
					hidden   : true,
					renderer : rendererDatetime
				}
			],
			selModel: sm,
			stripeRows: true,
			autoExpandColumn: 'page_url',
			height: 350,
			width: '98%',
			frame: true,
			border: true,
			title: TQSeo.sitemap.conf.lang.title,
			viewConfig: {
				forceFit: true,
				listeners: {
					refresh: function(view) {
						if (!Ext.isEmpty(TQSeo.sitemap.conf.criteriaFulltext)) {
							view.el.select('.x-grid3-body .x-grid3-cell').each(function(el) {
								TQSeo.highlightText(el.dom, TQSeo.sitemap.conf.criteriaFulltext);
							});
						}
					}
				}
			},
			tbar: [
				TQSeo.sitemap.conf.lang.labelSearchFulltext,
		    	{
		    		xtype: 'textfield',
		    		id: 'searchFulltext',
		    		fieldLabel: TQSeo.sitemap.conf.lang.labelSearchFulltext,
					emptyText : TQSeo.sitemap.conf.lang.emptySearchFulltext,
					listeners: {
						specialkey: function(f,e){
							if (e.getKey() == e.ENTER) {
								function_filter(this);
							}
						}
					}
		    	},
				{xtype: 'tbspacer', width: 10},
				TQSeo.sitemap.conf.lang.labelSearchPageUid,
		    	{
		    		xtype: 'numberfield',
		    		id: 'searchPageUid',
		    		fieldLabel: TQSeo.sitemap.conf.lang.labelSearchPageUid,
					emptyText : TQSeo.sitemap.conf.lang.emptySearchPageUid,
					width: 50,
					listeners: {
						specialkey: function(f,e){
							if (e.getKey() == e.ENTER) {
								function_filter(this);
							}
						}
					}
		    	},
				{xtype: 'tbspacer', width: 10},
				TQSeo.sitemap.conf.lang.labelSearchPageLanguage,
		    	{
		    		xtype: 'combo',
		    		id: 'searchPageLanguage',
		    		fieldLabel: TQSeo.sitemap.conf.lang.labelSearchPageLanguage,
					emptyText : TQSeo.sitemap.conf.lang.emptySearchPageLanguage,
					listeners: {
						select: function(f,e){
							function_filter(this);
						}
					},
					forceSelection: true,
					editable: false,
					mode: 'local',
					triggerAction: 'all',
					store: new Ext.data.ArrayStore({
						id: 0,
						fields: [
							'id',
							'label',
							'flag'
						],
						data: TQSeo.sitemap.conf.dataLanguage
					}),
					valueField: 'id',
					displayField: 'label',
					tpl: '<tpl for="."><div class="x-combo-list-item">{flag}{label}</div></tpl>'
		    	},
				{xtype: 'tbspacer', width: 10},
				TQSeo.sitemap.conf.lang.labelSearchPageDepth,
		    	{
		    		xtype: 'combo',
		    		id: 'searchPageDepth',
		    		fieldLabel: TQSeo.sitemap.conf.lang.labelSearchPageDepth,
					emptyText : TQSeo.sitemap.conf.lang.emptySearchPageDepth,
					listeners: {
						select: function(f,e){
							function_filter(this);
						}
					},
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
						data: TQSeo.sitemap.conf.dataDepth
					}),
					valueField: 'id',
					displayField: 'label'
		    	},
				{xtype: 'tbspacer', width: 10},
				{
					xtype: 'button',
					id: 'filterButton',
					text: TQSeo.sitemap.conf.filterIcon,
					handler: function_filter
				}
			],
			bbar: [
				{
					id: 'recordPaging',
					xtype: 'paging',
					store: gridDs,
					pageSize: TQSeo.sitemap.conf.pagingSize,
					displayInfo: true,
					displayMsg: TQSeo.sitemap.conf.lang.pagingMessage,
					emptyMsg: TQSeo.sitemap.conf.lang.pagingEmpty
				}, '->', {
					/****************************************************
					 * Delete button
					 ****************************************************/

                    xtype: 'button',
                    width: 80,
                    id: 'deleteButton',
                    text: TQSeo.sitemap.conf.lang.buttonDelete,
                    title: TQSeo.sitemap.conf.lang.buttonDeleteHint,
                    iconCls: 'delete',
                    cls: 'x-btn-over',
                    handleMouseEvents: false,
                    handler: function_delete
				}
			]
		});

	}

};