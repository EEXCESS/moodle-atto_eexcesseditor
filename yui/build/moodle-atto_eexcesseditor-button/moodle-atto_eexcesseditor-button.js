YUI.add('moodle-atto_eexcesseditor-button', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    atto_eexcesseditor
 * @copyright  bit media e-solutions GmbH <gerhard.doppler@bitmedia.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_eexcesseditor-button
 */

/**
 * Atto text editor eexcesseditor plugin.
 *
 * @namespace M.atto_eexcesseditor
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

Y.namespace('M.atto_eexcesseditor').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    selectedRec:[],
    citationStyles:false,
    citStyleList:[],
    userID:null,
   
	
    initializer: function () {
        // add buttons and tie methods to them
        var that = this;
        window.postMessage({
                    event: 'eexcess.newDashboardSettings',
                    settings: {
                        selectedChart: 'timeline',
                        hideCollections: false,
                        showLinkImageButton: true,
                        showLinkItemButton: true,
                        showScreenshotButton: true
                    }
                },'*');
        that.citationStyles = this.get('defaultCitStyle');
        var citStyleList = this.get('citStyles');
        this.citStyleList = citStyleList;
        that.userID = this.get("userId");
        //window.console.log(citStyleList);
        window.console.log(citStyleList);
        var citOpts = [];
        for(var i = 0;i<citStyleList.length;i++){
            window.console.log(citStyleList[i].val);
            var opt = {
                        text:citStyleList[i].label,
                        callback:function(e,args){
                            window.console.log(args);
                          that.saveSelectedCitation(args);
                        },
                        callbackArgs:citStyleList[i].val
                    };
            citOpts.push(opt);
        }
        citOpts.push({
            text:"Insert Link",
            callback:function(e,args){
                that.saveSelectedCitation(args);
            },
            callbackArgs:'lnk'
          });

        
        this.addButton({
            icon: 'icon',
            title: "opendashboard",
            iconComponent:'atto_eexcesseditor',
            buttonName: 'eexcesseditor',
            callback: function(){
                window.postMessage({event:'eexcess.openDashboard'},'*');
            }
          });

        this.addToolbarMenu({
            icon: 'iconcitstyles',
            title: 'citationstyle',
            iconComponent:'atto_eexcesseditor',
            callback:function(){},
            items:citOpts
        });
        window.addEventListener('message',function(e){
            if(e.data.event=="eexcess.queryTriggered"){
                that.selectedRec = [];
            }else if(e.data.event=='eexcess.linkItemClicked'){
				window.console.log("atto plugin received clicked link: "+e.data.data.id);
				// trying to hide dashboard
				that.selectedRec=[e.data.data];
				that.requireCitations();
                
			}else if(e.data.event=='eexcess.linkImageClicked'){
				window.console.log("atto plugin received image: "+e.data.data.id);
				that.selectedRec=[e.data.data];
				that.insertImage();
                window.postMessage({event:'eexcess.log.itemCitedAsImage',data:that.selectedRec},'*');
			} else if(e.data.event == 'eexcess.screenshot'){
				window.console.log("atto plugin received screenshot: "+e.data.data);
				that.insertScreenshot(e.data.data);
			}
        });
        
        this.get('host').editor.on('key',function(){
            var txt = that.getText();
            window.console.log('Querying for text: "'+  txt+'"');
            window.postMessage({event:'eexcess.paragraphEnd',text:that.getText()},'*');
        },'enter');

    },
    insertCit:function(){
        if(this.citationStyles == "lnk"){
            this.insertLink();
        }
        else{
            this.requireCitations();
        }
    },
	hideDashboard:function(){
		var container=document.getElementById("eexcess_container");
		container.style.visibility="hidden";
		document.getElementById("eexcess_button").className = "sym-eexcess";
	},
	showDashboard:function(){
		var container=document.getElementById("eexcess_container");
		container.style.visibility="visible";
	},
	insertCitationToEditor:function(s){
		var host=this.get('host');
		window.console.log('got to insert: '+s);
        host.focus();
		host.insertContentAtFocusPoint(s + '<br/>');
		this.hideDashboard();
    },
	insertScreenshot:function(imagesrc){
        window.console.log('imagesrc');
        window.console.log(imagesrc);
        var that = this;
        var url = M.cfg.wwwroot + '/lib/editor/atto/plugins/eexcesseditor/savescreen.php';

        Y.io(url,{
            data: {
                imgdata:imagesrc
            },
            method: 'POST',
            on:{
                success:function(r,arg){
                    window.console.log("response");
                    window.console.log(arg);
                    var imagetag="<img src='"+decodeURI(arg.response)+"'/>";
                    that.insertCitationToEditor(imagetag);
                }
            }
        });
    },
    insertLink:function(){
        var sel = this.selectedRec;
        this.lastUsedCitationStyle='insertLink';
        
        if(!sel.length){
            window.console.log("Nothing is selected");
            return false;
        }else{
            for(var i = 0;i<sel.length;i++){
				var link = sel[i],
				insLink = '<a href ="'+link.uri+'" target="_blank">'+link.title+'</a> ';
				this.insertCitationToEditor(insLink);
			}       
        }
    },
    insertImage:function(){
        var sel = this.selectedRec;
        if(!sel.length){
            window.console.log("Nothing is selected");
            return false;
        }else{
         for(var i = 0; i<sel.length;i++){
             var img = window.document.createElement('img'),
                 image = sel[i];
                img.src = image.previewImage;
                if(img.src !== image.previewImage){
                    var link = sel[i],
                    insLink = '<a href =""'+link.uri+'>'+link.title+'</a> ';
                    this.insertCitationToEditor(insLink);
                }else{
                    this.insertCitationToEditor(img.outerHTML);
                    window.console.log(img);
                }
            }
        }
    },
    requireCitations:function(){
        var style = this.citationStyles;
        if(!this.selectedRec.length){
            window.console.log("Nothing is selected");
            return false;
        }
        var that = this;
        if(style == 'lnk'){
            that.insertLink();
            window.postMessage({event:'eexcess.log.itemCitedAsHyperlink',data:that.selectedRec},'*');
            return false;
        }
        require(['local_eexcess/citationBuilder'],function(CitationProcessor){
            var citjson = that.parseRecToCitation(that.selectedRec);
            var cit = null;
				that.lastUsedCitationStyle=style;
                cit = new CitationProcessor(citjson,undefined,style);
                window.postMessage({event:'eexcess.log.itemCitedAsText',data:that.selectedRec},'*');
            that.insertCitationToEditor(cit);
        });
    },
    parseRecToCitation: function(recomendations){
        var citationJSONList = {};
        for(var i = 0;i<recomendations.length;i++){
            var r = recomendations[i],
                id = typeof r.id === 'undefined' ? "":r.id,
                collectionName = typeof r.collectionName === 'undefined' ? "":r.collectionName,
                uri = typeof r.uri === 'undefined'?"":r.uri,
                title = typeof r.title ==='undefined'?"":r.title,
                creator = typeof r.creator ==='undefined'?"":r.creator,
                year = typeof r.facets.year ==='undefined'?"":r.facets.year;
                
            var citObj = {
                    "id":id,
                    "container-title":collectionName,
                    "URL":uri,
                    "title":title,
                    "author":[{"family":creator}],
                    "issued":{"date-parts":[[year]]}
                };
           citationJSONList[citObj.id] = citObj;
           }
           
       return citationJSONList;
    },
    
    getText: function(){
        var host = this.get('host'),
            nodes = this.getNodesUntilCursor(host.editor.getDOMNode()),
            text = null;

        for(var i = (nodes.childNodes.length - 1);i>-1;i--){
            var node = nodes.childNodes[i];
            if(node.textContent.length>0){
                text = node.textContent;
                i = -1;
            }
        }
        return text;
    },
    getNodesUntilCursor: function (element) {
        var caretOffset = 0;
        if (typeof window.getSelection != "undefined") {
            var range = window.getSelection().getRangeAt(0);
            var preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(element);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            caretOffset = preCaretRange.toString().length;
            return preCaretRange.cloneContents();
        } else if (typeof document.selection != "undefined" && document.selection.type != "Control") {
            var textRange = document.selection.createRange();
            var preCaretTextRange = document.body.createTextRange();
            preCaretTextRange.moveToElementText(element);
            preCaretTextRange.setEndPoint("EndToEnd", textRange);
            caretOffset = preCaretTextRange.text.length;
            return preCaretTextRange.cloneContents();
        }
    },
    saveSelectedCitation:function(styleId){
        var url = M.cfg.wwwroot + '/lib/editor/atto/plugins/eexcesseditor/savecit.php';
        var userid = this.userID;
        if(styleId =='lnk'){
            this.citationStyles = styleId;
        }else{
            this.citationStyles = this.citStyleList[styleId].content;
        }
        this.requireCitations();
        Y.io(url,{
            data: {
                userid:userid,
                citstyle:styleId
            },
            method: 'POST',
            on:{
                success:function(r){
                    window.console.log("response");
                    window.console.log(r);
                }
            }
        });
    }
}, {
    ATTRS:{
        citStyles:{
            value:false
        },
        defaultCitStyle:{
            value:false
        },
        userId:{
            value:false
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
