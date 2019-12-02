import React, { Component } from 'react';
import axios from 'axios';
import './VideoRoomComponent.css';
import { OpenVidu } from 'openvidu-browser';
import StreamComponent from './stream/StreamComponent';
import DialogExtensionComponent from './dialog-extension/DialogExtension';
import ChatComponent from './chat/ChatComponent';

import VideoRoomLayout from './layout/layout';
import UserModel from './models/user-model';
import ToolbarComponent from './toolbar/ToolbarComponent';
import swal from 'sweetalert';
import { store as Notify } from 'react-notifications-component';

var localUser = new UserModel();

class VideoRoomComponent extends Component {
    constructor(props) {
        super(props);
        this.OPENVIDU_SERVER_URL = this.props.openviduServerUrl
            ? this.props.openviduServerUrl
            : 'https://' + window.location.hostname + ':4443';
        this.OPENVIDU_SERVER_SECRET = this.props.openviduSecret ? this.props.openviduSecret : 'MY_SECRET';
        this.hasBeenUpdated = false;
        this.layout = new VideoRoomLayout();
        let sessionName = this.props.sessionName ? this.props.sessionName : 'SessionA';
        let UserLoggedIn = this.props.userLoggedIn ? this.props.userLoggedIn : false;
        let userName = this.props.user ? this.props.user : 'unknown';
        let userEmail = this.props.userEmail ? this.props.userEmail : 'unknown';
        let userVerified = this.props.userVerified ? this.props.userVerified : false;
        let userCountry = this.props.userCountry ? this.props.userCountry : '';
        let userAge = this.props.userAge ? this.props.userAge : '';
        this.state = {
            myUserLoggedIn: UserLoggedIn,
            mySessionId: sessionName,
            myUserName: userName,
            myUserEmail: userEmail,
            myUserVerified: userVerified,
            myUserCountry: userCountry,
            myUserAge: userAge,
            session: undefined,
            localUser: undefined,
            subscribers: [],
            chatDisplay: 'none',
        };

        this.chatref = React.createRef();
        this.joinSession = this.joinSession.bind(this);
        this.goChatRoom = this.goChatRoom.bind(this);
        this.subscribeToGoChat = this.subscribeToGoChat.bind(this);
        this.leaveSession = this.leaveSession.bind(this);
        this.onbeforeunload = this.onbeforeunload.bind(this);
        this.updateLayout = this.updateLayout.bind(this);
        this.camStatusChanged = this.camStatusChanged.bind(this);
        this.micStatusChanged = this.micStatusChanged.bind(this);
        this.nicknameChanged = this.nicknameChanged.bind(this);
        this.toggleFullscreen = this.toggleFullscreen.bind(this);
        this.screenShare = this.screenShare.bind(this);
        this.stopScreenShare = this.stopScreenShare.bind(this);
        this.closeDialogExtension = this.closeDialogExtension.bind(this);
        this.toggleChat = this.toggleChat.bind(this);
        this.checkNotification = this.checkNotification.bind(this);
        this.checkSize = this.checkSize.bind(this);
        this.addFriend = this.addFriend.bind(this)
        this.saveMsg = this.saveMsg.bind(this)
        this.sendMsg = this.sendMsg.bind(this)
    }

    componentDidMount() {
        const openViduLayoutOptions = {
            maxRatio: 3 / 2, // The narrowest ratio that will be used (default 2x3)
            minRatio: 9 / 16, // The widest ratio that will be used (default 16x9)
            fixedRatio: false, // If this is true then the aspect ratio of the video is maintained and minRatio and maxRatio are ignored (default false)
            bigClass: 'OV_big', // The class to add to elements that should be sized bigger
            bigPercentage: 0.8, // The maximum percentage of space the big ones should take up
            bigFixedRatio: false, // fixedRatio for the big ones
            bigMaxRatio: 3 / 2, // The narrowest ratio to use for the big elements (default 2x3)
            bigMinRatio: 9 / 16, // The widest ratio to use for the big elements (default 16x9)
            bigFirst: true, // Whether to place the big one in the top left (true) or bottom right
            animate: true, // Whether you want to animate the transitions
        };

        this.layout.initLayoutContainer(document.getElementById('layout'), openViduLayoutOptions);
        window.addEventListener('beforeunload', this.onbeforeunload);
        window.addEventListener('resize', this.updateLayout);
        window.addEventListener('resize', this.checkSize);
        this.joinSession();
    }

    componentWillUnmount() {
        window.removeEventListener('beforeunload', this.onbeforeunload);
        window.removeEventListener('resize', this.updateLayout);
        window.removeEventListener('resize', this.checkSize);
        this.leaveSession();
    }

    onbeforeunload(event) {
        this.leaveSession();
    }

    joinSession() {
        this.OV = new OpenVidu();

        this.setState(
            {
                session: this.OV.initSession(),
            },
            () => {
                this.subscribeToStreamCreated();

                this.connectToSession();
            },
        );
    }

    connectToSession() {
        if (this.props.token !== undefined) {
            console.log('token received: ', this.props.token);
            this.connect(this.props.token);
        } else {
            this.getToken().then((token) => {
                console.log(token);
                this.connect(token);
            }).catch((error) => {
                if(this.props.error){
                    this.props.error({ error: error.error, messgae: error.message, code: error.code, status: error.status });
                }
                console.log('There was an error getting the token:', error.code, error.message);
                // alert('There was an error getting the token:', error.message);
              });
        }
    }

    connect(token) {
        this.state.session
            .connect(
                token,
                { clientUser: this.state.myUserName,
                    clientEmail: this.state.myUserEmail,
                    clientVerified: this.state.myUserVerified,
                    clientCountry: this.state.myUserCountry,
                    clientAge: this.state.myUserAge },
            )
            .then(() => {
                this.connectWebCam();
            })
            .catch((error) => {
                if(this.props.error){
                    this.props.error({ error: error.error, messgae: error.message, code: error.code, status: error.status });
                }
                // alert('There was an error connecting to the session:', error.message);
                console.log('There was an error connecting to the session:', error.code, error.message);
            });
    }

    connectWebCam() {
        let publisher = this.OV.initPublisher(undefined, {
            audioSource: undefined,
            videoSource: undefined,
            publishAudio: localUser.isAudioActive(),
            publishVideo: localUser.isVideoActive(),
            resolution: '640x480',
            frameRate: 30,
            insertMode: 'APPEND',
        });

        if (this.state.session.capabilities.publish) {
            this.state.session.publish(publisher).then(() => {
                if (this.props.joinSession) {
                    this.props.joinSession();
                }
            });
        }
        localUser.setNickname(this.state.myUserName);
        localUser.setEmail(this.state.myUserEmail);
        localUser.setVerified(this.state.myUserVerified);
        localUser.setCountry(this.state.myUserCountry);
        localUser.setAge(this.state.myUserAge);
        localUser.setConnectionId(this.state.session.connection.connectionId);
        localUser.setScreenShareActive(false);
        localUser.setStreamManager(publisher);
        this.subscribeToUserChanged();
        this.subscribeToStreamDestroyed();
        this.sendSignalUserChanged({ isScreenShareActive: localUser.isScreenShareActive() });

        this.setState({ localUser: localUser }, () => {
            this.state.localUser.getStreamManager().on('streamPlaying', (e) => {
                this.updateLayout();
                publisher.videos[0].video.parentElement.classList.remove('custom-class');
            });
        });
    }

    subscribeToGoChat() {
        let that = this;
        this.state.session.on('signal:gochat', (event) => {
            that.leaveSession();
            window.location = that.props.chatroomurl;
        });
    }

    goChatRoom() {
        if(this.chatref.current.toggleChatBox){
            this.chatref.current.toggleChatBox();
        }
        // if(!this.props.isfriend) return;
        // const signalOptions = {
        //     data: JSON.stringify({}),
        //     type: 'gochat',
        // };
        // this.state.session.signal(signalOptions);
        // this.leaveSession();
        // window.location = this.props.chatroomurl;
    }

    leaveSession() {
        const mySession = this.state.session;

        if (mySession) {
            mySession.disconnect();
        }

        // Empty all properties...
        this.OV = null;
        this.setState({
            session: undefined,
            subscribers: [],
            mySessionId: 'SessionA',
            myUserName: 'OpenVidu_User' + Math.floor(Math.random() * 100),
            localUser: undefined,
        });
        if (this.props.leaveSession) {
            this.props.leaveSession();
        }
    }
    camStatusChanged() {
        localUser.setVideoActive(!localUser.isVideoActive());
        localUser.getStreamManager().publishVideo(localUser.isVideoActive());
        this.sendSignalUserChanged({ isVideoActive: localUser.isVideoActive() });
        this.setState({ localUser: localUser });
    }

    micStatusChanged() {
        localUser.setAudioActive(!localUser.isAudioActive());
        localUser.getStreamManager().publishAudio(localUser.isAudioActive());
        this.sendSignalUserChanged({ isAudioActive: localUser.isAudioActive() });
        this.setState({ localUser: localUser });
    }

    nicknameChanged(nickname) {
        let localUser = this.state.localUser;
        localUser.setNickname(nickname);
        this.setState({ localUser: localUser });
        this.sendSignalUserChanged({ nickname: this.state.localUser.getNickname() });
    }

    deleteSubscriber(stream) {
        const remoteUsers = this.state.subscribers;
        const userStream = remoteUsers.filter((user) => user.getStreamManager().stream === stream)[0];
        let index = remoteUsers.indexOf(userStream, 0);
        if (index > -1) {
            remoteUsers.splice(index, 1);
            this.setState({
                subscribers: remoteUsers,
            });
        }
    }

    subscribeToStreamCreated() {
        this.state.session.on('streamCreated', (event) => {
            const subscriber = this.state.session.subscribe(event.stream, undefined);
            var subscribers = this.state.subscribers;
            subscriber.on('streamPlaying', (e) => {
                this.checkSomeoneShareScreen();
                subscriber.videos[0].video.parentElement.classList.remove('custom-class');
            });
            const newUser = new UserModel();
            newUser.setStreamManager(subscriber);
            newUser.setConnectionId(event.stream.connection.connectionId);
            newUser.setType('remote');
            const clientData = event.stream.connection.data.split('%')[0];

            newUser.setNickname(JSON.parse(clientData).clientUser);
            newUser.setEmail(JSON.parse(clientData).clientEmail);
            newUser.setVerified(JSON.parse(clientData).clientVerified);
            newUser.setCountry(JSON.parse(clientData).clientCountry);
            newUser.setAge(JSON.parse(clientData).clientAge);
            subscribers.push(newUser);
            this.setState(
                {
                    subscribers: subscribers,
                },
                () => {
                    if (this.state.localUser) {
                        this.sendSignalUserChanged({
                            isAudioActive: this.state.localUser.isAudioActive(),
                            isVideoActive: this.state.localUser.isVideoActive(),
                            nickname: this.state.localUser.getNickname(),
                            email: this.state.localUser.getEmail(),
                            verified: this.state.localUser.getVerified(),
                            country: this.state.localUser.getCountry(),
                            age: this.state.localUser.getAge(),
                            isScreenShareActive: this.state.localUser.isScreenShareActive(),
                        });
                    }
                    this.updateLayout();
                },
            );

            this.subscribeToGoChat();
        });
    }

    subscribeToStreamDestroyed() {
        // On every Stream destroyed...
        this.state.session.on('streamDestroyed', (event) => {
            // Remove the stream from 'subscribers' array
            this.deleteSubscriber(event.stream);
            setTimeout(() => {
                this.checkSomeoneShareScreen();
            }, 20);
            event.preventDefault();
            this.updateLayout();
        });
    }

    subscribeToUserChanged() {
        this.state.session.on('signal:userChanged', (event) => {
            let remoteUsers = this.state.subscribers;
            remoteUsers.forEach((user) => {
                if (user.getConnectionId() === event.from.connectionId) {
                    const data = JSON.parse(event.data);
                    console.log('EVENTO REMOTE: ', event.data);
                    if (data.isAudioActive !== undefined) {
                        user.setAudioActive(data.isAudioActive);
                    }
                    if (data.nickname !== undefined) {
                        user.setNickname(data.nickname);
                    }
                    if (data.email !== undefined) {
                        user.setEmail(data.email);
                    }
                    if (data.country !== undefined) {
                        user.setCountry(data.country);
                    }
                    if (data.age !== undefined) {
                        user.setAge(data.age);
                    }
                    if (data.isScreenShareActive !== undefined) {
                        user.setScreenShareActive(data.isScreenShareActive);
                    }
                }
            });
            this.setState(
                {
                    subscribers: remoteUsers,
                },
                () => this.checkSomeoneShareScreen(),
            );
        });
    }

    updateLayout() {
        setTimeout(() => {
            this.layout.updateLayout();
        }, 20);
    }

    sendSignalUserChanged(data) {
        const signalOptions = {
            data: JSON.stringify(data),
            type: 'userChanged',
        };
        this.state.session.signal(signalOptions);
    }

    toggleFullscreen() {
        const document = window.document;
        const fs = document.getElementById('opv_container');
        if (
            !document.fullscreenElement &&
            !document.mozFullScreenElement &&
            !document.webkitFullscreenElement &&
            !document.msFullscreenElement
        ) {
            if (fs.requestFullscreen) {
                fs.requestFullscreen();
            } else if (fs.msRequestFullscreen) {
                fs.msRequestFullscreen();
            } else if (fs.mozRequestFullScreen) {
                fs.mozRequestFullScreen();
            } else if (fs.webkitRequestFullscreen) {
                fs.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    }

    screenShare() {
        const videoSource = navigator.userAgent.indexOf('Firefox') !== -1 ? 'window' : 'screen';
        const publisher = this.OV.initPublisher(
            undefined,
            {
                videoSource: videoSource,
                publishAudio: localUser.isAudioActive(),
                publishVideo: localUser.isVideoActive(),
                mirror: false,
            },
            (error) => {
                if (error && error.name === 'SCREEN_EXTENSION_NOT_INSTALLED') {
                    this.setState({ showExtensionDialog: true });
                } else if (error && error.name === 'SCREEN_SHARING_NOT_SUPPORTED') {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Your browser does not support screen sharing'
                      })
                } else if (error && error.name === 'SCREEN_EXTENSION_DISABLED') {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'You need to enable screen sharing extension'
                      })
                } else if (error && error.name === 'SCREEN_CAPTURE_DENIED') {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'You need to choose a window or application to share'
                      })
                }
            },
        );

        publisher.once('accessAllowed', () => {
            this.state.session.unpublish(localUser.getStreamManager());
            localUser.setStreamManager(publisher);
            this.state.session.publish(localUser.getStreamManager()).then(() => {
                localUser.setScreenShareActive(true);
                this.setState({ localUser: localUser }, () => {
                    this.sendSignalUserChanged({ isScreenShareActive: localUser.isScreenShareActive() });
                });
            });
        });
        publisher.on('streamPlaying', () => {
            this.updateLayout();
            publisher.videos[0].video.parentElement.classList.remove('custom-class');
        });
    }

    closeDialogExtension() {
        this.setState({ showExtensionDialog: false });
    }

    stopScreenShare() {
        this.state.session.unpublish(localUser.getStreamManager());
        this.connectWebCam();
    }

    checkSomeoneShareScreen() {
        let isScreenShared;
        // return true if at least one passes the test
        isScreenShared = this.state.subscribers.some((user) => user.isScreenShareActive()) || localUser.isScreenShareActive();
        const openviduLayoutOptions = {
            maxRatio: 3 / 2,
            minRatio: 9 / 16,
            fixedRatio: isScreenShared,
            bigClass: 'OV_big',
            bigPercentage: 0.8,
            bigFixedRatio: false,
            bigMaxRatio: 3 / 2,
            bigMinRatio: 9 / 16,
            bigFirst: true,
            animate: true,
        };
        this.layout.setLayoutOptions(openviduLayoutOptions);
        this.updateLayout();
    }

    toggleChat(property) {
        let display = property;

        if (display === undefined) {
            display = this.state.chatDisplay === 'none' ? 'block' : 'none';
        }
        if (display === 'block') {
            this.setState({ chatDisplay: display, messageReceived: false });
        } else {
            console.log('chat', display);
            this.setState({ chatDisplay: display });
        }
        this.updateLayout();
    }

    checkNotification(event) {
        this.setState({
            messageReceived: true,
        });
    }
    checkSize() {
        if (document.getElementById('layout').offsetWidth <= 700 && !this.hasBeenUpdated) {
            this.toggleChat('none');
            this.hasBeenUpdated = true;
        }
        if (document.getElementById('layout').offsetWidth > 700 && this.hasBeenUpdated) {
            this.hasBeenUpdated = false;
        }
    }

    addFriend() {
        if(!this.state.myUserLoggedIn){
            swal({
                icon: 'info',
                text: this.props.t("You didn't log in!")
            })
            return;
        }
        if(!this.state.myUserVerified){
            swal({
                icon: 'info',
                text: this.props.t("You didn't complete your profile!")
            })
            return;
        }

        let that = this;
        this.state.subscribers.map((sub, i) => {
            if(sub.getEmail() == 'unknown'){
                swal({
                    icon: 'info',
                    text: this.props.t("This person didn't log in!")
                })
                return false;
            }
            if(sub.getNickname() == 'unknown'){
                swal({
                    icon: 'info',
                    text: this.props.t("This person didn't verified!")
                })
                return false;
            }

            console.log(sub.getEmail());
            axios.get(this.props.friendrequesturl, {
                params: {
                    friend: sub.getEmail()
                }
            })
            .then( function(res) {
                if (res.data.success) {
                    Notify.addNotification({
                        title: that.props.t("Wonderful!"),
                        message: that.props.t("Invitation succesfully sent"),
                        type: "success",
                        insert: "top",
                        container: "top-right",
                        animationIn: ["animated", "fadeIn"],
                        animationOut: ["animated", "fadeOut"],
                        dismiss: {
                          duration: 5000,
                          onScreen: true
                        }
                      });
                } else {
                    if (res.data.error === 'exist') {
                        Notify.addNotification({
                            title: that.props.t("Warning!"),
                            message: that.props.t("Friend already added"),
                            type: "warning",
                            insert: "top",
                            container: "top-right",
                            animationIn: ["animated", "fadeIn"],
                            animationOut: ["animated", "fadeOut"],
                            dismiss: {
                              duration: 5000,
                              onScreen: true
                            }
                        });
                    }
                    else if (res.data.error === 'pending') {
                        Notify.addNotification({
                            title: that.props.t("Warning!"),
                            message: that.props.t("This person already sent invitation"),
                            type: "warning",
                            insert: "top",
                            container: "top-right",
                            animationIn: ["animated", "fadeIn"],
                            animationOut: ["animated", "fadeOut"],
                            dismiss: {
                              duration: 5000,
                              onScreen: true
                            }
                        });
                    }
                }
            })
        })

    }

    sendMsg(email, type, message) {
        return new Promise((resolve, reject) => {
            axios.get(this.props.sendmsgurl, {
                params: {
                    to: email,
                    type: type,
                    message: message
                }
            })
            .then( function(res) {
                if (res.data.result=='ok') {
                    resolve(res.data.msgid);
                }
            })
            .catch( function(res) {
                reject(res);
            })
        });
    }

    saveMsg(type, message) {
        return new Promise((resolve, reject) => {
            if(!this.state.myUserLoggedIn){
                reject("not auth");
            }
            if(!this.state.myUserVerified){
                reject("not verified");
            }
            let that = this;
            if(this.state.subscribers.length > 0){
                var sub = this.state.subscribers[0]
                if(sub.getEmail() == 'unknown'){
                    reject("partner not auth");
                }
                if(sub.getNickname() == 'unknown'){
                    reject("partner not verified");
                }
                that.sendMsg(
                    sub.getEmail(),
                    type,
                    message)
                .then((res) => {
                    resolve({email: sub.getEmail(), id: res});
                })
                .catch( function(res) {
                    reject(res);
                })
            }
            reject();
        })
    }

    render() {
        const mySessionId = this.state.mySessionId;
        const localUser = this.state.localUser;
        const {t} = this.props;
        return (
            <div className="container" id="opv_container">
                <ToolbarComponent
                    sessionId={mySessionId}
                    user={localUser}
                    showNotification={this.state.messageReceived}
                    camStatusChanged={this.camStatusChanged}
                    micStatusChanged={this.micStatusChanged}
                    screenShare={this.screenShare}
                    stopScreenShare={this.stopScreenShare}
                    toggleFullscreen={this.toggleFullscreen}
                    leaveSession={this.leaveSession}
                    toggleChat={this.goChatRoom}
                />

                <DialogExtensionComponent showDialog={this.state.showExtensionDialog} cancelClicked={this.closeDialogExtension} />

                <div id="layout" className="bounds">
                    {this.state.subscribers.map((sub, i) => (
                        <div key={i} className="OT_root OT_publisher custom-class" id="remoteUsers">
                            <StreamComponent user={sub} streamId={sub.streamManager.stream.streamId} />
                        </div>
                    ))}
                    {localUser !== undefined && localUser.getStreamManager() !== undefined && (
                        <div className="OT_root OT_publisher custom-class" id="localUser">
                            <StreamComponent user={localUser} handleNickname={this.nicknameChanged} />
                        </div>
                    )}
                </div>
                {localUser !== undefined && localUser.getStreamManager() !== undefined && (
                    <ChatComponent
                        t={t}
                        user={localUser}
                        close={this.toggleChat}
                        messageReceived={this.checkNotification}
                        lang={this.props.lang}
                        translateurl={this.props.translateurl}
                        saveMsg={this.saveMsg}
                        readmsgtransurl={this.props.readmsgtransurl}
                        readmsgurl={this.props.readmsgurl}
                        ref={this.chatref}
                    />
                )}
                <div className="action-wrapper">
                    <button onClick={this.props.endChat}>{t("End Chat")}</button>
                    {!this.props.isfriend && <div onClick={this.addFriend}><i className="fa fa-user-plus"></i></div>}
                    {this.props.shownextaction && <button onClick={this.props.findNext}>{t("Find Next")}</button>}
                </div>
            </div>
        );
    }

    /**
     * --------------------------
     * SERVER-SIDE RESPONSIBILITY
     * --------------------------
     * These methods retrieve the mandatory user token from OpenVidu Server.
     * This behaviour MUST BE IN YOUR SERVER-SIDE IN PRODUCTION (by using
     * the API REST, openvidu-java-client or openvidu-node-client):
     *   1) Initialize a session in OpenVidu Server	(POST /api/sessions)
     *   2) Generate a token in OpenVidu Server		(POST /api/tokens)
     *   3) The token must be consumed in Session.connect() method
     */

    getToken() {
        return this.createSession(this.state.mySessionId).then((sessionId) => this.createToken(sessionId));
    }

    createSession(sessionId) {
        return new Promise((resolve, reject) => {
            var data = JSON.stringify({ customSessionId: sessionId });
            axios
                .post(this.OPENVIDU_SERVER_URL + '/api/sessions', data, {
                    headers: {
                        Authorization: 'Basic ' + btoa('OPENVIDUAPP:' + this.OPENVIDU_SERVER_SECRET),
                        'Content-Type': 'application/json',
                    },
                })
                .then((response) => {
                    console.log('CREATE SESION', response);
                    resolve(response.data.id);
                })
                .catch((response) => {
                    var error = Object.assign({}, response);
                    if (error.response && error.response.status === 409) {
                        resolve(sessionId);
                    } else {
                        console.log(error);
                        console.warn(
                            'No connection to OpenVidu Server. This may be a certificate error at ' + this.OPENVIDU_SERVER_URL,
                        );
                        if (
                            window.confirm(
                                'No connection to OpenVidu Server. This may be a certificate error at "' +
                                    this.OPENVIDU_SERVER_URL +
                                    '"\n\nClick OK to navigate and accept it. ' +
                                    'If no certificate warning is shown, then check that your OpenVidu Server is up and running at "' +
                                    this.OPENVIDU_SERVER_URL +
                                    '"',
                            )
                        ) {
                            window.location.assign(this.OPENVIDU_SERVER_URL + '/accept-certificate');
                        }
                    }
                });
        });
    }

    createToken(sessionId) {
        return new Promise((resolve, reject) => {
            var data = JSON.stringify({ session: sessionId });
            axios
                .post(this.OPENVIDU_SERVER_URL + '/api/tokens', data, {
                    headers: {
                        Authorization: 'Basic ' + btoa('OPENVIDUAPP:' + this.OPENVIDU_SERVER_SECRET),
                        'Content-Type': 'application/json',
                    },
                })
                .then((response) => {
                    console.log('TOKEN', response);
                    resolve(response.data.token);
                })
                .catch((error) => reject(error));
        });
    }
}
export default React.forwardRef((props, ref) => <VideoRoomComponent
  ref={ref} {...props}
/>);
