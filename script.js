function loadNews(){
	// changes the colour of the news tab to distinguish which page is open
	$("#newsTab").css("background-color","#ffaa00");
	$("#newsTab").css("color","#3945cc");
	$("#newsTab").css("border-bottom","2px solid #ffaa00");
}

function loadGlobal(){
	$("#globalTab").css("background-color","#ffaa00");
	$("#globalTab").css("color","#3945cc");
	$("#globalTab").css("border-bottom","2px solid #ffaa00");
	$(document).ready(function(){
		$("#messages").scrollTop(100000000);
		// AJAX function to refresh only the messages on the page every 2 seconds
		setInterval(function(){
				$("#messages").load("messages.php");
				$("#messages").scrollTop(100000000);
			},2000
		);
	});
}

function loadParent(){
	$("#parentTab").css("background-color","#ffaa00");
	$("#parentTab").css("color","#3945cc");
	$("#parentTab").css("border-bottom","2px solid #ffaa00");
	$(document).ready(function(){
		$("#messages").scrollTop(100000000);
		// AJAX function to refresh only the messages on the page every 2 seconds
		setInterval(function(){
				$("#messages").load("messages.php");
				$("#messages").scrollTop(100000000);
			},2000
		);
	});
}

function loadPrivate(){
	$("#privateTab").css("background-color","#ffaa00");
	$("#privateTab").css("color","#3945cc");
	$("#privateTab").css("border-bottom","2px solid #ffaa00");
}

function loadPrivateChat(){
	$("#privateTab").css("background-color","#ffaa00");
	$("#privateTab").css("color","#3945cc");
	$("#privateTab").css("border-bottom","2px solid #ffaa00");
	$(document).ready(function(){
		$("#messages").scrollTop(100000000);
		if (memberFound == true){
			// AJAX function to refresh only the messages on the page every 2 seconds
			setInterval(function(){
					$("#messages").load("messages.php");
					$("#messages").scrollTop(100000000);
				},2000
			);
			$("#messageSendArea").toggle();
		}
	});
}

function loadProfile(){
	$("#profileTab").css("background-color","#ffaa00");
	$("#profileTab").css("color","#3945cc");
	$("#profileTab").css("border-bottom","2px solid #ffaa00");
}

function loadReports(){
	$("#reportsTab").css("background-color","#ffaa00");
	$("#reportsTab").css("color","#3945cc");
	$("#reportsTab").css("border-bottom","2px solid #ffaa00");
}

function selectIcon(icon){
	icon = icon.toString(); // converts icon into a string to be able to be manipulated
	//reset the style of all options of icon
	for (i=0; i<=8; i++){
		if (icon.length > 1){ // if the icon has an additional number on the end do this
			$("#"+i+icon[1]).css("width","60px");
			$("#"+i+icon[1]).css("height","60px");
			$("#"+i+icon[1]).css("border","none");
		} else {
			$("#"+i).css("width","60px");
			$("#"+i).css("height","60px");
			$("#"+i).css("border","none");
		}
	}
	// sets the selected icon to have a black border
	$("#"+icon).css("width","56px");
	$("#"+icon).css("height","56px");
	$("#"+icon).css("border","2px solid #000");
}

function selectSticker(sticker){
	// clear border of all sticker buttons and draw border around selected sticker button
}

$(document).ready(function(){
	// button functions
	$("#changeIconCloseButton").click(function(){
		$("#updateIcon").hide();
	});
	$("#chooseStickerCloseButton").click(function(){
		$("#stickerChoice").hide();
		$("#messageEntryBoxCool").focus();
	});
	$("#friendRequestCloseButton").click(function(){
		$("#createRequestAlertBox").hide();
	});
	$("#createChildCloseButton").click(function(){
		$('#newChildAlertBox').hide();
	});
	$("#changeTitleCloseButton").click(function(){
		$(`#changeName`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	$("#chatInviteCloseButton").click(function(){
		$(`#inviteFriend`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	$("#acceptInvitationButton").click(function(){
		window.location='acceptInvitation.php';
	});
	$("#declineInvitationButton").click(function(){
		window.location=`declineInvitation.php`;
	});
	$("#deleteChatButton").click(function(){
		window.location=`deleteChat.php`;
	});
	$("#cancelDeleteChatButton").click(function(){
		$(`#deletePrivate`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	$("#membersCloseButton").click(function(){
		$(`#membersList`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	$("#changeStyleCloseButton").click(function(){
		$(`#changeStyle`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	$("#createChatCloseButton").click(function(){
		$(`#createChat`).toggle();
		$('#chatSearch').focus();
	});
	$("#signUpCloseButton").click(function(){
		$(`#finishSignUpBox`).hide();
	});
	$("#messageCloseButton").click(function(){
		$(`#messageAlertBox`).toggle();
	});
	$("#userIcon").click(function(){
		$(`#updateIcon`).toggle();
	});
	$("#stopDeleteFriend").click(function(){
		$("#unfriend").hide();
	});
	$("#stopDeleteUser").click(function(){
		window.location='profile.php'
	});
	$("#stickersButton").click(function(){
		$("#stickerChoice").toggle();
		$("#messageEntryBoxCool").focus();
	});
	$("#inviteUserButton").click(function(){
		var username = $("#usernameToInvite").val();
		$.post(`inviteUser.php`,{userToInvite: username},function(data,status){
			$(`#messagePopupText`).prepend(data).show();
		});
		$(`#inviteFriend`).hide();
		$(`#messageEntryBoxCool`).focus();
	});
	
	// send message
	$("#sendMessageButtonID").click(function(){
		var messageBody = $("#messageEntryBoxCool").val();
		$.post("sendMessage.php",{messageBody: messageBody});
		$("#messageEntryBoxCool").val("");
	});
});