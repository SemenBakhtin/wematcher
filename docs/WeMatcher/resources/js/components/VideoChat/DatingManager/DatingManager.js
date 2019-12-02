import React, { Component, forwardRef } from 'react';
import ReactDOM from 'react-dom';
import VideoRoomComponent from '../VideoRoomComponent/VideoRoomComponent';
import { withTranslation, I18nextProvider } from "react-i18next";
import i18n from "../../i18n";

class DatingManager extends Component {

    constructor(props) {
        super(props);

        this.state = {
            ...props
        }

        this.roomref = React.createRef();

        i18n.changeLanguage(this.props.lang);
        this.checkSize = this.checkSize.bind(this);
        this.endChat = this.endChat.bind(this);
    }

    checkSize() {
        $('.chatWrapper').height($('.chatWrapper').width()*2/3);
        if($('.chatWrapper').width()<=640){
            $('.chatWrapper').height($('.chatWrapper').height()*2);
        }
    }

    componentDidMount() {
        Echo.private('Dating.' + this.state.logininfo.id)
            .listen('.CallEnd', (e) => {
                window.location.href = e.redirect_url;
            });
        window.addEventListener('resize', this.checkSize);
        this.checkSize();
    }

    endChat() {
        window.location = this.state.endurl;
    }

    goChatRoom() {
        if(this.roomref.current.goChatRoom){
            this.roomref.current.goChatRoom();
        }
    }

    render() {
      return (
        <div style={{position:'relative'}}>
            <div className="chatWrapper">
                <VideoRoomComponent
                    {...this.props}
                    ref={this.roomref}
                    id="opv-room"
                    openviduServerUrl={this.props.openvidu_server_url}
                    openviduSecret={this.props.openvidu_server_secret}
                    sessionName={this.props.openvidu_session_id}
                    userLoggedIn={true}
                    user={this.state.logininfo.person.name}
                    userEmail={this.state.logininfo.email}
                    userVerified={true}
                    userCountry={this.state.logininfo.person.country}
                    userAge={this.state.logininfo.person.age}
                    lang={this.props.lang}
                    shownextaction={false}
                    endChat={this.endChat}
                    isfriend={this.props.isfriend}
                    readmsgtransurl={this.props.readmsgtransurl}
                    readmsgurl={this.props.readmsgurl}
                    sendmsgurl={this.props.sendmsgurl}
                    chatroomurl={this.props.chatroomurl}
                />
            </div>
        </div>
      );
    }
}

const DatingManager_ = withTranslation('translations', { withRef: true })(DatingManager);

const DatingManager__ = forwardRef((props, ref) => {
    return (<I18nextProvider i18n={i18n}>
                <DatingManager_ {...(props)} ref={ref}/>
            </I18nextProvider>);
});

document.querySelectorAll('.dating_component').forEach(function(component) {

    var props = Object.assign({}, component.dataset);

    props.logininfo = JSON.parse(props.logininfo);

    if(props.isfriend != '0'){
        props.isfriend = true;
    }
    else{
        props.isfriend = false;
    }

    ReactDOM.render(
        <DatingManager__ {...props} ref={(datingComponent) => {window.datingComponent = datingComponent}}/>
        , component);
})
