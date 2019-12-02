import React, { Component, forwardRef } from 'react'
import ReactNotification from 'react-notifications-component'
import ReactDOM from 'react-dom';
import 'react-notifications-component/dist/theme.css'
import { store as Notify } from 'react-notifications-component';
import { withTranslation, I18nextProvider } from "react-i18next";
import i18n from "../i18n";

class Notification extends Component {

    constructor(props) {
        super(props);

        i18n.changeLanguage(this.props.lang);
    }

    notify = (title, message, type) => {
        Notify.addNotification({
            title: title,
            message: message,
            type: type,
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

    componentDidMount() {
        const {t} = this.props;
        if(this.props.userid){
            Echo.private('App.Models.User.'+this.props.userid)
                .notification((notification) => {
                    if(notification.type == "App\\Notifications\\AddFriendRequestAcceptNotification"){
                        Notify.addNotification({
                            title: t("Wonderful!"),
                            message: notification.from_name + t(" accepted your friend invitation."),
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
                    }
                    else if(notification.type == "App\\Notifications\\AddFriendRequestRejectNotification"){
                        Notify.addNotification({
                            title: t("Warning!"),
                            message: notification.from_name + t(" cancelled friendship."),
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
                    else if(notification.type == "App\\Notifications\\AddFriendRequestNotification"){
                        Notify.addNotification({
                            title: t("Wonderful!"),
                            message: notification.from_name + t(" wants to add you as a friend."),
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
                    }
                });
        }
    }

    render() {
        return (
            <div className="notification-container">
                <ReactNotification />
            </div>
        );
    }
}

const Notification_ = withTranslation('translations', { withRef: true })(Notification);

const Notification__ = forwardRef((props, ref) => {
    return (<I18nextProvider i18n={i18n}>
                <Notification_ {...props} ref={ref}/>
            </I18nextProvider>);
});

var component = document.getElementById('wematcher_notification');
var props = Object.assign({}, component.dataset);

ReactDOM.render(
    <Notification__ {...props} ref={(notificationComponent) => {window.notificationComponent = notificationComponent}}/>
    , component);
