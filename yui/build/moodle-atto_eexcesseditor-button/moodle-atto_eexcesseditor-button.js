YUI.add('moodle-atto_eexcesseditor-button', function (Y, NAME) {

Y.namespace('M.atto_eexcesseditor').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    selectedRec:[],
    citationStyles:[],
    initializer: function () {
        // add buttons and tie methods to them
        var that = this;
        that.citationStyles = this.get('citStyles');
        var citOpts = [];
        for(var i = 0;i<that.citationStyles.length;i++){
            var opt = {
                        text:that.citationStyles[i].name,
                        callback:that.onToolbarMenuItemClick
                    }
            citOpts.push(opt);
        };
        citOpts.push({
            text:"Insert Link",
            callback:that.insertLink
          });
        citOpts.push({
            text:"Insert Image",
            callback:that.insertImage,
            
          });
        
        this.addButton({
            icon: 'icon',
            iconComponent:'atto_eexcesseditor',
            buttonName: 'eexcesseditor',
            callback: this.onButtonClick
          });
          
        this.addToolbarMenu({
            icon: 'icon',
            iconComponent:'atto_eexcesseditor',
            callback:function(){},
            items:citOpts
        });
        window.addEventListener('message',function(e){
            if(e.data.event=='eexcess.selectionChanged'){
                that.selectedRec = [];
                that.selectedRec = e.data.selected;   
            }else if(e.data.event=="eexcess.queryTriggered"){
                that.selectedRec = [];
            }
        });
        this.get('host').editor.on('key',function(){
            var txt = that.getText();
            window.console.log('Querying for text: "'+  txt+'"');
            window.postMessage({event:'eexcess.paragraphEnd',text:that.getText()},'*');
        },'enter');

    },
    insertLink:function(){
        var host = this.get('host'),
            sel = this.selectedRec;
        
        if(!sel.length){
            window.console.log("Nothing is selected");
            return false;
        }else{
                for(var i = 0;i<sel.length;i++){
                var link = sel[i],
                insLink = '<a href =""'+link.uri+'>'+link.title+'</a> ';
                host.insertContentAtFocusPoint(insLink + '</br>');
           }
            
                
        }
    },
    insertImage:function(){
        var host = this.get('host');
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
                    host.insertContentAtFocusPoint(insLink + '</br>');
                }else{
                    host.insertContentAtFocusPoint(img.outerHTML +'</br>');
                    window.console.log(img);
                }
            }
        }
    },
   
    onButtonClick:function(){
        this.requireCitations();
        
    },
    onToolbarMenuItemClick:function(e){
        var idx = e.target.getData("index"),
            style = this.citationStyles[idx].style;
        window.console.log(style);
        this.requireCitations(style);
        
    },
    
    requireCitations:function(style){
        if(!this.selectedRec.length){
            window.console.log("Nothing is selected");
            return false;
        }
        var that = this;
        
        require(['local_eexcess/citationBuilder'],function(CitationProcessor){
            var citjson = that.parseRecToCitation(that.selectedRec);
            var cit = null;
            window.console.log("selected records")
            window.console.log(citjson);
            if(typeof style === 'undefined'){
                //window.console.log("running with generic");
                cit = CitationProcessor(citjson);    
            }else{
                //window.console.log("running with specific style");
                cit = CitationProcessor(citjson,undefined,style);
            }
            that.insertCitations(cit);
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
                }
           citationJSONList[citObj.id] = citObj;
           }
           
       return citationJSONList;  
    },
    insertCitations:function(c){
        var host = this.get('host');
        for(var i = 0;i<c.length;i++){
            var cit = c[i];
            host.insertContentAtFocusPoint(cit+'<br>');
        }
        
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
    }
}, {
    ATTRS:{
        citStyles:{
            value:false
        }
    }
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
