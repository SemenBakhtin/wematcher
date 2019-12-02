import React, { Component } from 'react';
import IconButton from '@material-ui/core/IconButton';
import Fab from '@material-ui/core/Fab';
import HighlightOff from '@material-ui/icons/HighlightOff';
import Send from '@material-ui/icons/Send';
import SelectLanguageDlg from '../../../SelectLanguageDlg/SelectLanguageDlg'

import './ChatComponent.css';
import { Tooltip } from '@material-ui/core';

class ChatComponent extends Component {
    constructor(props) {
        super(props);
        this.state = {
            messageList: [],
            message: '',
            langdlgopen: false,
            translate:{
                auto: true,
                lang: props.lang
            },
            bshow: true,
            top: $('#wematcher_app').height() - 400 - $('.btn-chatpage').height()
        };

        this.bshow = true;
        this.chatScroll = React.createRef();

        this.handleChange = this.handleChange.bind(this);
        this.toggleChatBox = this.toggleChatBox.bind(this);
        this.handlePressKey = this.handlePressKey.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
        this.handleLangDlgClose = this.handleLangDlgClose.bind(this);
        this.selectLanguage = this.selectLanguage.bind(this);
    }

    translate(data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.props.translateurl,
                data: data,
                method: 'GET',
            })
            .done(response => {
                response = JSON.parse(response);
                resolve(response.translatedText);
            })
            .fail(response => {
                resolve(data.text);
            });
        });
    }

    readMsg(msgid){
        axios.get(this.props.readmsgurl, {
            params: {
                msgid: msgid
            }
        })
    }

    readMsgTrans(msgid, lang, translated_message){
        axios.get(this.props.readmsgtransurl, {
            params: {
                msgid: msgid,
                lang: lang,
                translated_message: translated_message
            }
        })
    }

    componentDidMount() {
        this.props.user.getStreamManager().stream.session.on('signal:chat', (event) => {
            const data = JSON.parse(event.data);
            let messageList = this.state.messageList;
            if (this.state.translate.auto && event.from.connectionId != this.props.user.getConnectionId()) {
                let that=this;
                this.translate({
                    'text': data.message,
                    'lang': this.state.translate.lang
                }).then((res) => {
                    if(this.props.user && data.msgids!=0){
                        if(data.msgids.email == this.props.user.getEmail()){
                            this.readMsgTrans(data.msgids.id, this.state.translate.lang, res);
                        }
                    }

                    messageList.push({ connectionId: event.from.connectionId, nickname: data.nickname, original: data.message, message:res, email: data.email, verified: data.verified, translate: true });
                    that.setState({ messageList: messageList });
                    this.scrollToBottom();
                    setTimeout(() => {
                        this.props.messageReceived();
                    }, 50);
                })
            } else {
                if(this.props.user && data.msgids!=0){
                    if(data.msgids.email == this.props.user.getEmail()){
                        this.readMsg(data.msgids.id);
                    }
                }

                messageList.push({ connectionId: event.from.connectionId, nickname: data.nickname, original: data.message, message:data.message, email: data.email, verified: data.verified, translate: false });
                this.setState({ messageList: messageList });
                this.scrollToBottom();
                setTimeout(() => {
                    this.props.messageReceived();
                }, 50);
            }

        });
    }

    handleChange(event) {
        this.setState({ message: event.target.value });
    }

    handlePressKey(event) {
        if (event.key === 'Enter') {
            this.sendMessage();
        }
    }

    sendMessage() {
        if (this.props.user && this.state.message) {
            let message = this.state.message.replace(/ +(?= )/g, '');
            if (message !== '' && message !== ' ') {
                this.props.saveMsg(
                    "text", message
                    )
                    .then((res) => {
                        if(res != 0) {
                            const data = { message: message, nickname: this.props.user.getNickname(), email: this.props.user.getEmail(), verified: this.props.user.getVerified(), streamId: this.props.user.getStreamManager().stream.streamId, msgids: res };
                            this.props.user.getStreamManager().stream.session.signal({
                                data: JSON.stringify(data),
                                type: 'chat',
                            });
                        }
                    })
                    .catch((res) => {
                        const data = { message: message, nickname: this.props.user.getNickname(), email: this.props.user.getEmail(), verified: this.props.user.getVerified(), streamId: this.props.user.getStreamManager().stream.streamId, msgids: 0 };
                        this.props.user.getStreamManager().stream.session.signal({
                            data: JSON.stringify(data),
                            type: 'chat',
                        });
                    })
            }
        }
        this.setState({ message: '' });
    }


    scrollToBottom() {
        setTimeout(() => {
            $('.message').stop().animate({
                'scrollTop': $('.message-inner').height() + 100
            }, 0, 'swing', function () {

            });
        }, 10);
    }

    selectLanguage(){
        this.setState({
            ...this.state,
            langdlgopen: true
        })
    }

    handleLangDlgClose = value => {
        console.log(value);
        this.setState({
            ...this.state,
            translate: {
                auto: value.auto,
                lang: value.lang
            },
            langdlgopen: false
        })
    }

    toggleChatBox() {
        this.setState({
            ...this.state,
            bshow: !this.state.bshow
        })
    }

    render() {
        return (
            <div id="chatContainer" style={{top: this.state.top, display: this.state.bshow?'block':'none'}}>
                <div className="message">
                    <div className="message-inner">
                        {this.state.messageList.map((data, i) => (
                            <div
                                key={i}
                                id="remoteUsers"
                                className={
                                    'item-wrapper' + (data.connectionId !== this.props.user.getConnectionId() ? ' received' : ' send')
                                }
                            >
                                <div className="item clearfix">
                                    <span className="float-left"><i className="sender">{data.connectionId === this.props.user.getConnectionId() ? 'Me' : data.nickname}}</i>: </span>
                                    <div className="float-left ml-2">
                                        <span>{data.message}</span>
                                        <br/>
                                        {data.connectionId !== this.props.user.getConnectionId() && data.translate == true && data.original != data.message && <span className="original">{data.original}</span>}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <div id="messageInput">
                    <img src="/img/mob_language.png" onClick={this.selectLanguage} className="lang_img"/>
                    {this.state.translate.auto && <i className="fa fa-check autochecked" aria-hidden="true"></i>}
                    <input
                        placeholder={this.props.t("Send a messge")}
                        id="chatInput"
                        value={this.state.message}
                        onChange={this.handleChange}
                        onKeyPress={this.handlePressKey}
                    />
                    <Tooltip title="Send message">
                        <Fab size="small" id="sendButton" onClick={this.sendMessage}>
                            <Send />
                        </Fab>
                    </Tooltip>
                    <SelectLanguageDlg t={this.props.t} open={this.state.langdlgopen} onClose={this.handleLangDlgClose} translate={this.state.translate}/>
                </div>
            </div>
        );
    }
}

export default React.forwardRef((props, ref) => <ChatComponent
  ref={ref} {...props}
/>);
