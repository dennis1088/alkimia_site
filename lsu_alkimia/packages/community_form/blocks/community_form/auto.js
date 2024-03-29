var miniSurvey ={
	bid:0,
	serviceURL: $("input[name=miniSurveyServices]").val() + '?block=form&',
	init: function(){ 
			this.tabSetup();
			this.answerTypes=document.forms['ccm-block-form'].answerType;
			this.answerTypesEdit=document.forms['ccm-block-form'].answerTypeEdit; 

			for(var i=0;i<this.answerTypes.length;i++){
				this.answerTypes[i].onclick=function(){miniSurvey.optionsCheck(this);miniSurvey.settingsCheck(this);}
				this.answerTypes[i].onchange=function(){miniSurvey.optionsCheck(this);miniSurvey.settingsCheck(this);}
			} 
			for(var i=0;i<this.answerTypesEdit.length;i++){
				this.answerTypesEdit[i].onclick=function(){miniSurvey.optionsCheck(this,'Edit');miniSurvey.settingsCheck(this,'Edit');}
				this.answerTypesEdit[i].onchange=function(){miniSurvey.optionsCheck(this,'Edit');miniSurvey.settingsCheck(this,'Edit');}
			} 			
			$('#refreshButton').click( function(){ miniSurvey.refreshSurvey() } );
			$('#addQuestion').click(   function(){ miniSurvey.addQuestion()   } );
			$('#editQuestion').click(  function(){ miniSurvey.addQuestion('Edit')   } );
			$('#cancelEditQuestion').click(   function(){ $('#editQuestionForm').css('display','none') } );			
			this.serviceURL+='cID='+this.cID+'&arHandle='+this.arHandle+'&bID='+this.bID+'&btID='+this.btID+'&';
			miniSurvey.refreshSurvey();
		},	
	tabSetup: function(){
		$('ul#ccm-formblock-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-formblock-tab-','');
				miniSurvey.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		$('ul#ccm-formblock-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-formblock-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-formBlockPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-formBlockPane-'+pane).css('display','block');
	},
	refreshSurvey : function(){
			$.ajax({ 
					url: this.serviceURL+'mode=refreshSurvey&qsID='+parseInt(this.qsID)+'&hide='+miniSurvey.hideQuestions.join(','),
					success: function(msg){ $('#miniSurveyPreviewWrap').html(msg); }
				});
			$.ajax({ 
					url: this.serviceURL+'mode=refreshSurvey&qsID='+parseInt(this.qsID)+'&showEdit=1&hide='+miniSurvey.hideQuestions.join(','),
					success: function(msg){	$('#miniSurveyWrap').html(msg); }
				});			
		},
	optionsCheck : function(radioButton,mode){
			if(mode!='Edit') mode='';
			if( radioButton.value=='select' || radioButton.value=='radios' || radioButton.value=='checkboxlist'){
				 $('#answerOptionsArea'+mode).css('display','block');
			}else $('#answerOptionsArea'+mode).css('display','none');			
		},
	settingsCheck : function(radioButton,mode){
			if(mode!='Edit') mode='';
			if( radioButton.value=='text'){
				 $('#answerSettings'+mode).css('display','block');
			}else $('#answerSettings'+mode).css('display','none');			
		},
	addQuestion : function(mode){ 
			var msqID=0;
			if(mode!='Edit') mode='';
			else msqID=parseInt($('#msqID').val())
			var postStr='question='+encodeURIComponent($('#question'+mode).val())+'&options='+encodeURIComponent($('#answerOptions'+mode).val());
			postStr+='&width='+escape($('#width'+mode).val());
			postStr+='&height='+escape($('#height'+mode).val());
			var req=($('#required'+mode).get(0).checked)?1:0;
			postStr+='&required='+req;
			postStr+='&position='+escape($('#position'+mode).val());
			var form=document.getElementById('ccm-block-form'); 
			var opts=form['answerType'+mode];
			var answerType='';
			for(var i=0;i<opts.length;i++){
				if(opts[i].checked){
					answerType=opts[i].value;
					break;
				}
			} 
			postStr+='&inputType='+answerType;//$('input[name=answerType'+mode+']:checked').val()
			postStr+='&msqID='+msqID+'&qsID='+parseInt(this.qsID);			
			$.ajax({ 
					type: "POST",
					data: postStr,
					url: this.serviceURL+'mode=addQuestion&qsID='+parseInt(this.qsID),
					success: function(msg){ 
						eval('var jsonObj='+msg);
						if(!jsonObj){
						   alert(ccm_t('ajax-error'));
						}else if(jsonObj.noRequired){
						   alert(ccm_t('complete-required'));
						}else{
						   if(jsonObj.mode=='Edit'){
							   $('#questionEditedMsg').slideDown('slow');
							   setTimeout("$('#questionEditedMsg').slideUp('slow');",5000);
							   if(jsonObj.hideQID){
								   miniSurvey.hideQuestions.push( miniSurvey.edit_qID ); //jsonObj.hideQID); 
								   miniSurvey.edit_qID=0;
							   }
						   }else{
							   $('#questionAddedMsg').slideDown('slow');
							   setTimeout("$('#questionAddedMsg').slideUp('slow');",5000);
							   //miniSurvey.saveOrder();
						   }
						   $('#editQuestionForm').css('display','none');
						   miniSurvey.qsID=jsonObj.qsID;
						   miniSurvey.ignoreQuestionId(jsonObj.msqID);
						   $('#qsID').val(jsonObj.qsID);
						   miniSurvey.resetQuestion();
						   miniSurvey.refreshSurvey();						  
						   //miniSurvey.showPane('preview');
						}
					}
				});
	},
	//prevent duplication of these questions, for block question versioning
	ignoreQuestionId:function(msqID){
		var msqID, ignoreEl=$('#ccm-ignoreQuestionIDs');
		if(ignoreEl.val()) msqIDs=ignoreEl.val().split(',');
		else msqIDs=[];
		msqIDs.push( parseInt(msqID) );
		ignoreEl.val( msqIDs.join(',') );
	},
	reloadQuestion : function(qID){
			
			$.ajax({ 
				url: this.serviceURL+"mode=getQuestion&qsID="+parseInt(this.qsID)+'&qID='+parseInt(qID),
				success: function(msg){				
						eval('var jsonObj='+msg);
						$('#editQuestionForm').css('display','block')
						$('#questionEdit').val(jsonObj.question);
						$('#answerOptionsEdit').val(jsonObj.optionVals.replace(/%%/g,"\r\n") );
						$('#widthEdit').val(jsonObj.width);
						$('#heightEdit').val(jsonObj.height); 
						$('#positionEdit').val(jsonObj.position); 
						if( parseInt(jsonObj.required)==1 ) 
							 $('#requiredEdit').get(0).checked=true;
						else $('#requiredEdit').get(0).checked=false;
						$('#msqID').val(jsonObj.msqID);    
						for(var i=0;i<miniSurvey.answerTypesEdit.length;i++){							
							if(miniSurvey.answerTypesEdit[i].value==jsonObj.inputType){
								miniSurvey.answerTypesEdit[i].checked=true; 
								miniSurvey.optionsCheck(miniSurvey.answerTypesEdit[i],'Edit');
								miniSurvey.settingsCheck(miniSurvey.answerTypesEdit[i],'Edit');
							}
						}
						if(parseInt(jsonObj.bID)>0) 
							miniSurvey.edit_qID = parseInt(qID) ;
						scroll(0,165);
					}
			});
	},	
	//prevent duplication of these questions, for block question versioning
	pendingDeleteQuestionId:function(msqID){
		var msqID, el=$('#ccm-pendingDeleteIDs');
		if(el.val()) msqIDs=ignoreEl.val().split(',');
		else msqIDs=[];
		msqIDs.push( parseInt(msqID) );
		el.val( msqIDs.join(',') );
	},	
	hideQuestions : [], 
	deleteQuestion : function(el,msqID,qID){
			if(confirm(ccm_t('delete-question'))) { 
				$.ajax({ 
					url: this.serviceURL+"mode=delQuestion&qsID="+parseInt(this.qsID)+'&msqID='+parseInt(msqID),
					success: function(msg){	miniSurvey.resetQuestion(); miniSurvey.refreshSurvey();  }			
				});
				
				miniSurvey.ignoreQuestionId(msqID);
				miniSurvey.hideQuestions.push(qID); 
				miniSurvey.pendingDeleteQuestionId(msqID)
			}
	},
	resetQuestion : function(){
			$('#question').val('');
			$('#answerOptions').val('');
			$('#width').val('50');
			$('#height').val('3');
			$('#msqID').val('');
			for(var i=0;i<this.answerTypes.length;i++){
				this.answerTypes[i].checked=false;
			}
			$('#answerOptionsArea').hide();
			$('#answerSettings').hide();
			$('#required').get(0).checked=0;
	},
	
	validate:function(){
			var failed=0;
			
			var n=$('#ccmSurveyName');
			if( !n || parseInt(n.val().length)==0 ){
				alert(ccm_t('form-name'));
				this.showPane('options');
				n.focus();
				failed=1;
			}
			
			var Qs=$('.miniSurveyQuestionRow'); 
			if( !Qs || parseInt(Qs.length)<1 ){
				alert(ccm_t('form-min-1'));
				failed=1;
			}
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	},
	
	moveUp:function(el,thisQID){
		var qIDs=this.serialize();
		var previousQID=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisQID){
				if(previousQID==0) break; 
				$('#miniSurveyQuestionRow'+thisQID).after($('#miniSurveyQuestionRow'+previousQID));
				break;
			}
			previousQID=qIDs[i];
		}	
		this.saveOrder();
	},
	moveDown:function(el,thisQID){
		var qIDs=this.serialize();
		var thisQIDfound=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisQID){
				thisQIDfound=1;
				continue;
			}
			if(thisQIDfound){
				$('#miniSurveyQuestionRow'+qIDs[i]).after($('#miniSurveyQuestionRow'+thisQID));
				break;
			}
		}
		this.saveOrder();
	},
	serialize:function(){
		var t = document.getElementById("miniSurveyPreviewTable");
		var qIDs=[];
		for(var i=0;i<t.childNodes.length;i++){ 
			if( t.childNodes[i].className && t.childNodes[i].className.indexOf('miniSurveyQuestionRow')>=0 ){ 
				var qID=t.childNodes[i].id.substr('miniSurveyQuestionRow'.length);
				qIDs.push(qID);
			}
		}
		return qIDs;
	},
	saveOrder:function(){ 
		var postStr='qIDs='+this.serialize().join(',')+'&qsID='+parseInt(this.qsID);
		$.ajax({ 
			type: "POST",
			data: postStr,
			url: this.serviceURL+"mode=reorderQuestions",			
			success: function(msg){	
				miniSurvey.refreshSurvey();
			}			
		});
	},
	showRecipient:function(cb){ 
		if(cb.checked) $('#recipientEmailWrap').css('display','block');
		else $('#recipientEmailWrap').css('display','none');
	}
}
ccmValidateBlockForm = function() { return miniSurvey.validate(); }
//$(document).ready(function(){miniSurvey.init()});
