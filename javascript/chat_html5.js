var ws = null;
var chatType = 0;//��������
var wpUserName = null;//˽�Ķ���
var logined = "";
var smileIconMaxNumInOne = 5;//һ����༸������
$j(function () {
    
    reConnect();
});
function tmsg(str) {
    //console.log(str);
};
function closeHandler() {
    drop = 0;
    this.tmsg("�������ˣ�30����Զ�������", 0);
    recvMsg("CT|<font color=\"#c0c0c0\">�������ˣ�30����Զ�����,�����ֶ�ˢ�¡�</font>");
    setTimeout(this.reConnect, 30000);
    this.logined = false;
};
function reConnect() {
    // ��һ�� web socket
    var str = getSetting();
    var ip = str.split('|')[0];
    ws = new WebSocket("ws://"+ip+":1986");
    ws.onopen = function () {
        // Web Socket �������ϣ�ʹ�� send() ������������

        tmsg("an|���ӳɹ������ڵ�½��");
        loginChat();
        
    };;

    ws.onmessage = function (evt) {
        var received_msg = evt.data;
        tmsg("�����ѽ���:" + received_msg);
        onDataEvent(evt.data);
    };;

    ws.onclose = function () {
        // �ر� websocket
        tmsg("an|�����ѹر�...");
        clearInterval(heartNum);
        closeHandler();
    };;

};
function talk(param1) {
    var _loc2_ = null;
    var _loc3_ = null;
    // if (this.getTime() - this.lastSendMsgTime < 100) {
    //   recvMsg("SM|" + "��Ǹ,���Ե�(" + int((this.getTime() - this.lastSendMsgTime) / 1000) + ")!");
    //   this.tmsg("��Ǹ,���Ե�(" + int((this.getTime() - this.lastSendMsgTime) / 1000) + ")!", 0);
    //  return;
    // };
   
    this.tmsg("will send " + param1 + "-" + this.lastSendMsgTime, 1);
    if (!this.logined) {
        recvMsg("SM|" + "��Ǹ,��û�е�¼!");
        //  this.tmsg("���ȵ�¼!", 0);
        return;
    };
    if (param1 != "") {
        if (param1.substr(0, 2) == "#!" || param1.substr(0, 1) == "!" || param1.substr(0, 2) == "!!" || param1.substr(0, 1) == "#") {
            if (!ExternalInterface.call("callGMCommand", param1.substr(2))) {
                this.tmsg("����ʧ��!", 0);
            }
            else {
                this.tmsg("�����ɹ�!", 0);
            };
        };
        if (param1.substr(0, 2) == "//") {
            _loc2_ = param1.split(" ", 2);
            _loc3_ = _loc2_[0].substr(2);
            this.wpUserName = _loc3_;
            //  this.tmsg("�������Ķ���Ϊ��" + this.wpUserName, 0);
            _loc2_[1] = param1.substr(this.wpUserName.length + 2, 41);
            param1 = !!_loc2_[1] ? _loc2_[1] : "";
            //    this.cbWP.selected = true;
            //    setHWP(true, this.wpUserName);
        };
        if (this.chatType == 2) {
            ws.send("SGCHAT " + param1 + "\r\n");
        }
        else if (this.chatType == 3) {
            ws.send("GCHAT " + param1 + "\r\n");
        }
        else if (this.wpUserName != "" && this.chatType == 1) {
            ws.send("WP " + this.wpUserName + " " + param1);
        }
        else {
            ws.send("CHAT " + param1 + "\r\n");
        };
    };
    //this.lastSendMsgTime = Number(this.getTime());
    param1 = "";
};
//��¼����
function loginChat() {
    var str = getSetting();
    var ip = str.split('|')[0];
    var key = str.split('|')[1];
    ws.send("login " + key + " " + ip);


};
var heartNum = 0;
function heartTime() {
    ws.send("W 1");
};
var drop = 0;
function DecisionOffline() {
    drop++;
    if (drop >= 4) {
        //һ����û�нӵ�������Ӧ�����ж������ˣ�������ж��ɸ�������Լ��޸�
        closeHandler();
    };
};
function onDataEvent(param1) {


    var strArray = param1.split("|");
    if (strArray[0] == "W") {
        // this.lab_status.text = ".";
        DecisionOffline();
        return;
    };
    if (strArray.length < 2) {
        // this.lab_status.text = "Unknown message: " + src;
        return;
    };
    var command = strArray[0];
    var msg = strArray[1];
    while (msg.charCodeAt(msg.length - 1) == 13 || msg.charCodeAt(msg.length - 1) == 10) {
        return;
        msg = msg.substr(0, msg.length - 1);
        if (msg.length == 0) {
            return;
        };
    };

    if (command == "L") {
        recvMsg("SM|" + "���ӷ������ɹ���");
        whenConnect();
        logined = true;
        heartNum = setInterval(heartTime, 15000);//������


    }
    else if (command == "UL") {
        recvMsg("UL|" + msg);
    }
    else if (command == "UA") {
        recvMsg("UA|" + msg);
    }
    else if (command == "WP") {
        recvMsg("WP|" + this.showmsg("<font color=\'#0000FF\'>" + this.colorUser_SL(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "</font>\n"));
    }
    else if (command == "GC") {
        recvMsg("GC|" + this.showmsg("<font color=\'#4E7BE7\'>" + this.colorUser(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "</font>\n"));
    }
    else if (command == "SG") {
        recvMsg("SG|" + this.showmsg("<font color=\'#009900\'>" + this.colorUser(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "</font>\n"));
    }
    else if (command == "SYSI") {
        recvMsg("SI|" + this.showmsg("<font color=\'#FF0000\'>[����]��" + this.colorUser(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "</font>\n"));
    }
    else if (command == "an") {
        recvMsg("SI|" + ("<font color=\'#FF0000\'>[ϵͳ����]��" + msg + "</font>\n"));
    }
    else if (command == "SYSM") {
        recvMsg("SM|" + msg);
    }
    else if (command == "SYS") {
        recvMsg("SI|" + ("<font color=\'#C95C14\'>[ϵ ͳ]��" + this.colorUser(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "</font>\n"));
    }
    else if (command == "LE") {
        recvMsg("SM|" + strArray[1]);
    }
    else if (command == "SYSN") {
        try {
            AsCallBack(msg.substr(0, msg.length - 1));
        }
        catch (error) {
        };
    }
    else if (command == "es") {
        myPattern = /"event\:showTip3_([^"]+)"/gi;
        recvMsg("CT|" + this.showmsg(this.colorUser(msg.replace(myPattern, "\"javascript:showTip3(\'$1\',0,1,1,window.event);void(0);\"  onmouseout=\"UnTip3()\""), /\$([^`]+)\`/, "#ff0000")));
    }
    else if (command == "bs") {
        myPattern = /"event\:showBb_([^"]+)"/gi;
        recvMsg("CT|" + this.showmsg(this.colorUser(msg.replace(myPattern, "\"javascript:showBb(\'$1\',window.event);void(0);\""), /\$([^`]+)\`/, "#ff0000")));
    }
    else if (command == "C" || command == "CT") {
        spstr = "`˵��";
        pos = msg.indexOf(spstr) + spstr.length;
        if (msg.substr(pos, 2) == "!!") {
            msg = msg.substr(0, pos) + "<font color=\"blue\">" + msg.substr(pos + 2) + "</font>";
        };
        if (msg.substr(pos, 1) == "!") {
            msg = msg.substr(0, pos) + "<font color=\"#FF00FF\">" + msg.substr(pos + 1) + "</font>";
        };
        if (msg.substr(pos, 1) == "#") {
            msg = msg.substr(0, pos) + "<font color=\"green\">" + msg.substr(pos + 1) + "</font>";
        };
        recvMsg("CT|" + this.showmsg(this.colorUser(this.smileText(msg, /\[\$(\d+)\]/), /\$([^`]+)\`/, "#ff0000") + "\n"));
    }
    else {
        recvMsg("SM|src = " + src);
    }
    command = "";
};
function filterWord(param1) {
    return param1;
    var _loc2_ = 0;
    while (_loc2_ < this.badWords.length) {
        while (param1.indexOf(this.badWords[_loc2_]) > -1) {
            param1 = param1.replace(this.badWords[_loc2_], "*");
        };
        _loc2_++;
    };

    return param1;
};


function showmsg(param1) {
    while (param1.charCodeAt(param1.length - 1) == 13 || param1.charCodeAt(param1.length - 1) == 10) {
        param1 = param1.substr(0, param1.length - 1);
    };
    param1 = this.filterWord(param1);
    return param1;
};

;function colorUser(param1, param2, param3) {
    var _loc4_ = null;
    while (true) {
        _loc4_ = param1.match(param2);
        if (!_loc4_) {
            break
        }
        ;param1 = param1.replace(_loc4_[0], "<a onclick=\"$(\'cmsg\').value=\'//" + _loc4_[1] + " \';$(\'cmsg\').focus();void(0);\" name=\"javascript:$(\'cmsg\').value=\'//\"><font color=\'" + param3 + "\'>" + _loc4_[1] + "</font></a>")
    }
    ;return param1
}
function colorUser_SL(param1, param2, param3) {
    var _loc4_ = null;
    while (true) {
        _loc4_ = param1.match(param2);
        if (!_loc4_) {
            break;
        };
        var name =  _loc4_[1].split("$")[0];
        param1 = param1.replace(_loc4_[0],"<span><span style='vertical-align: middle;'><a onclick=\"$(\'cmsg\').value=\'//" + name + " \';$(\'cmsg\').focus();void(0);\" name=\"javascript:$(\'cmsg\').value=\'//\"><font color=\'" + param3 + "\'>" + name + "</font></a>");
    };
    return param1+"</span></span>";
};

function smileText(param1, param2) {
    var _loc3_ = null;
    var _loc4_ = 0;
    param1 = param1.replace(this.replaceStrOfImg, "   ");
    param2 = /\((\d+)\)/i;
    var _loc5_ = [];
    while (true) {
        _loc3_ = param1.match(param2);
        if (!_loc3_) {
            break;
        };
        if (this.smileIconMaxNumInOne > _loc4_) {
            _loc5_[_loc5_.length] = _loc3_[1];
            param1 = param1.replace(_loc3_[0], "<img src=images/ui/motion/" + _loc3_[1] + ".gif>");
        }
        else {
            param1 = param1.replace(_loc3_[0], "");
        };
        _loc4_++;
    };
    //console.log(param1);
    return param1;
};
function setChatType(param1) {
    chatType = param1;
};