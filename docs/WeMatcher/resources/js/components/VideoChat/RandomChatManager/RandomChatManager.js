import React, { Component, forwardRef } from 'react';
import ReactDOM from 'react-dom';
import CheckDevice from '../CheckDevice/CheckDevice';
import Searching from '../Searching/Searching';
import RandomChatIndex from '../RandomChatIndex/RandomChatIndex';
import './RandomChatManager.scss';
import VideoRoomComponent from '../VideoRoomComponent/VideoRoomComponent';
import swal from 'sweetalert';
import { withTranslation, I18nextProvider } from "react-i18next";
import i18n from "../../i18n";
import SearchButton from '../SearchButton/SearchButton';

class RandomChatManager extends Component {

    constructor(props) {
        super(props);

        i18n.changeLanguage(this.props.lang);

        this.socket = null;
        this.state = {
                        ...props,
                        turnOn: false,
                        message: '',
                        openvidu_session_id: '',
                        messages: [],
                        translate:{
                            auto: false,
                            lang: 'en'
                        },
                        partner: {
                            email: 'unknown',
                            partnerId: '',
                            name: 'unknown'
                        }
                    }
        this.turnOnCamera = this.turnOnCamera.bind(this)
        this.setPartner = this.setPartner.bind(this)
        this.findNext = this.findNext.bind(this)
        this.endChat = this.endChat.bind(this)
        this.disbleFindNext = this.disbleFindNext.bind(this)
        this.stopSearching = this.stopSearching.bind(this)
        this.search = this.search.bind(this)
        this.sendSocketData = this.sendSocketData.bind(this)
        this.componentDidMount = this.componentDidMount.bind(this)
        this.componentWillUnmount = this.componentWillUnmount.bind(this)
        this.checkSize = this.checkSize.bind(this);
        this.errBack = this.errBack.bind(this);
    }

    errBack(err) {
        this.setState({
            ...this.state,
            step: 0,
            turnOn: false
        })
        setTimeout(() => {
            console.log("The following error occurred: " + err.name);

            if (err.name == 'NotFoundError') {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: this.props.t('Camera not found!')
                })
            }
        }, 100)
    }

    turnOnCamera(){
        let that = this;

        this.setState({
            ...this.state,
            step: 1,
            turnOn: true
        })

        if(navigator.mozGetUserMedia) { // Mozilla-prefixed
            navigator.mozGetUserMedia({ audio: true, video: true }, function(stream){
                if(that.state.step==1 && ( that.state.turnOn || that.state.status=='finding' )){
                    let welcomeLocalVideo = document.getElementById('welcomeLocalVideo');
                    welcomeLocalVideo.srcObject = stream;
                }
            }, this.errBack);
        }
        else if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ audio: true, video: true }).then(function(stream) {
                if(that.state.step==1 && ( that.state.turnOn || that.state.status=='finding' )){
                    let welcomeLocalVideo = document.getElementById('welcomeLocalVideo');
                    welcomeLocalVideo.srcObject = stream;
                }
            }, this.errBack);
        }
        else if(navigator.getUserMedia) { // Standard
            navigator.getUserMedia({ audio: true, video: true }, function(stream) {
                if(that.state.step==1 && ( that.state.turnOn || that.state.status=='finding' )){
                    let welcomeLocalVideo = document.getElementById('welcomeLocalVideo');
                    welcomeLocalVideo.srcObject = stream;
                }
            }, this.errBack);
        } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
            navigator.webkitGetUserMedia({ audio: true, video: true }, function(stream){
                if(that.state.step==1 && ( that.state.turnOn || that.state.status=='finding' )){
                    let welcomeLocalVideo = document.getElementById('welcomeLocalVideo');
                    welcomeLocalVideo.srcObject = stream;
                }
            }, this.errBack);
        }
    }

    search() {
        this.turnOnCamera();
        this.setState({
            ...this.state,
            enableNext: true,
            messages: [],
            turnOn: false,
            status: 'finding',
            time: 0,
            step:1
        })

        this.sendSocketData({
            role: 'video',
            type: 'videoChatRequest',
            gender: this.state.gender,
            vgender: this.state.vgender,
            email: this.state.isloggedin ? this.state.logininfo.email : 'unknown',
            name: this.state.isloggedin && this.state.logininfo.person.status=='active' ? this.state.logininfo.person.name : 'unknown'
        })
    }

    sendSocketData(data) {
        this.socket.send(JSON.stringify(data));
    }

    closeSocket() {
        this.socket.close();
    }

    setPartner(data) {
        this.setState({
            ...this.state,
            partner: {
                ...this.state.partner,
                partnerId: data.partner,
                name: data.name,
                email: data.email
            },
            step: 2
        })
    }

    componentDidMount() {
        if (this.socket) {
            this.socket.close()
        }

        this.socket = new WebSocket(this.state.websocketurl);

        setInterval(() => {
            if(this.socket == null || this.socket.readyState == 2 || this.socket.readyState == 3){
                this.socket = new WebSocket(this.state.websocketurl);
            }
        }, 1000);

        this.socket.onopen = function(e) {
            console.log('socket established');
            if(that.state.status == 'finding'){
                that.search();
            }
        };

        this.socket.onclose = function(e) {
            that.setState({
                ...that.state,
                socketstatus: 'disconnected'
            })
        }

        let that = this;
        this.socket.onmessage = function (e) {

            let data = JSON.parse(e.data);
            console.log(data);

            if (data.role == 'video' || data.role == 'both') {
                const type = data.type;
                switch (type) {
                    case 'connected':
                        that.setPartner(data);

                        that.setState({
                            ...that.state,
                            openvidu_session_id: data.sessionId,
                            status: 'connected',
                            turnOn: false
                        })

                        that.checkSize();

                        console.log('connected')
                        break;
                    case 'disconnect':
                        if (data.user == that.state.partner.partnerId && that.state.status == 'connected') {
                            that.search();
                        }
                        break;
                    case 'endChat':
                        if (that.state.status == 'connected') {
                            that.search();
                        }
                        break;
                }
            }
        };

        window.addEventListener('resize', this.checkSize);
    }

    componentWillUnmount() {
        if (this.socket) {
            this.socket.close()
        }
    }
    checkSize() {
        if(this.state.step==2 && this.state.status=='connected'){
            $('.chatWrapper').height($('.chatWrapper').width()*2/3);
            if($('.chatWrapper').width()<=640){
                $('.chatWrapper').height($('.chatWrapper').height()*2);
            }
        }
    }

    findNext() {
        this.sendSocketData({
            role: 'video',
            type: 'endChat',
            to: this.state.partner.partnerId,
        });

        this.search();
    }

    endChat() {
        this.sendSocketData({
            role: 'video',
            type: 'endChat',
            to: this.state.partner.partnerId,
        });

        this.setState({
            ...this.state,
            status : 'unconnected',
            turnOn : false,
            step : 0
        })
    }

    disbleFindNext() {
        this.state.enableNext = false;
    }

    stopSearching() {
        this.sendSocketData({
            role: 'video',
            type: 'stopSearching',
        });

		this.setState({
            ...this.state,
            step: 0,
            status : 'unconnected'
        })
    }

    handleChange = event => {
        console.log(event.target.checked);
    };

    goChatRoom() {
        if(this.state.step==2 && this.state.status=='connected' && this.roomref){
            this.roomref.goChatRoom();
        }
    }

    render() {
        const {t} = this.props

      return (
        <div style={{position:'relative'}}>
            {this.state.step==0 &&
            <RandomChatIndex {...this.props} photourls={this.props.photourls} action={this.turnOnCamera} genderurl={this.props.genderurl}/>
            }
            {this.state.step==1 && this.state.turnOn &&
            <CheckDevice {...this.props} action={this.search} genderurl={this.props.genderurl}/>
            }

            {this.state.step==1 && this.state.status=='finding' &&
            <Searching {...this.props} action={this.stopSearching} genderurl={this.props.genderurl}/>
            }

            {this.state.step==2 && this.state.status=='connected' &&
            <div className="chatWrapper">
                <VideoRoomComponent
                    {...this.props}
                    onRef={ref => (this.roomref = ref)}
                    id="opv-room"
                    openviduServerUrl={this.props.openvidu_server_url}
                    openviduSecret={this.props.openvidu_server_secret}
                    sessionName={this.state.openvidu_session_id}
                    userLoggedIn={this.state.isloggedin}
                    user={this.state.isloggedin && this.state.logininfo.person.status=='active' ? this.state.logininfo.person.name : 'unknown'}
                    userEmail={this.state.isloggedin ? this.state.logininfo.email : 'unknown'}
                    userVerified={this.state.isloggedin && this.state.logininfo.person.status=='active'}
                    userCountry={this.state.isloggedin && this.state.logininfo.person.status=='active' ? this.state.logininfo.person.country : 'unknown'}
                    userAge={this.state.isloggedin && this.state.logininfo.person.status=='active' ? this.state.logininfo.person.age : 'unknown'}
                    lang={this.props.lang}
                    friendrequesturl={this.props.friendrequesturl}
                    shownextaction={true}
                    endChat={this.endChat}
                    findNext={this.findNext}
                    translateurl={this.props.translateurl}
                    isfriend={false}
                    readmsgtransurl={this.props.readmsgtransurl}
                    readmsgurl={this.props.readmsgurl}
                    sendmsgurl={this.props.sendmsgurl}
                />
            </div>
            }
        </div>
      );
    }
}

const RandomChatManager_ = withTranslation('translations', { withRef: true })(RandomChatManager);

const RandomChatManager__ = forwardRef((props, ref) => {
    return (<I18nextProvider i18n={i18n}>
                <RandomChatManager_ {...(props)} ref={ref}/>
            </I18nextProvider>);
});

document.querySelectorAll('.random_video_component').forEach(function(component) {

    var props = Object.assign({}, component.dataset);

    props.photourls = JSON.parse(props.photourls);
    props.isloggedin = parseInt(props.isloggedin);

    if(props.logininfo){
        props.logininfo = JSON.parse(props.logininfo);
    }

    ReactDOM.render(
        <RandomChatManager__ {...props} ref={(randomchatComponent) => {window.randomchatComponent = randomchatComponent}}/>
        , component);
})
