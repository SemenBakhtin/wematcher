import React, { Component } from 'react'
import ReactDOM from 'react-dom';
import axios from 'axios';
import './VideoCall.scss';
import Dialog from '@material-ui/core/Dialog';
import DialogContent from '@material-ui/core/DialogContent';

const styles = {
    root: {
      backgroundColor: "transparent"
    },

    paper: {
      backgroundColor: "transparent",
      boxShadow: "none",
      overflow: "hidden"
    },
  };

class VideoCall extends Component {

    constructor(props) {
        super(props);

        this.state = {
            status: 'none',
            partner_id: 0,
            partner_name: '',
            partner_avatar: '',
            end_url: '',
            accept_url: '',
            reject_url: '',
            cron_call: null
        }

        this.setStatus = this.setStatus.bind(this);
        this.accept = this.accept.bind(this);
        this.reject = this.reject.bind(this);
        this.end = this.end.bind(this);
    }

    setStatus = (status, partner_id, partner_name, partner_avatar, call_url, end_url, accept_url, reject_url) => {
        if( status == 'call'){
            if( this.state.status == 'call'){
                this.end();
            }

            var cron_call = setInterval(() => {
                axios.get(call_url);
            }, 1000);

            this.setState({
                status: 'call',
                partner_id: partner_id,
                partner_name: partner_name,
                partner_avatar: partner_avatar,
                call_url: call_url,
                end_url: end_url,
                accept_url: accept_url,
                reject_url: reject_url,
                cron_call: cron_call
            })
        }
        else if( status == 'incomingcall'){
            if( this.state.status == 'call' && this.state.partner_id == partner.id ){
                this.end();
            }
            this.setState({
                status: 'incomingcall',
                partner_id: partner_id,
                partner_name: partner_name,
                partner_avatar: partner_avatar,
                end_url: end_url,
                accept_url: accept_url,
                reject_url: reject_url
            })
        }
    }

    end() {
        axios.get(this.state.end_url);
        if(this.state.cron_call){
            clearInterval(this.state.cron_call);
        }

        this.setState({
            status: 'none',
            partner_id: 0,
            partner_name: '',
            partner_avatar: '',
            end_url: '',
            accept_url: '',
            reject_url: '',
            cron_call: null
        })
    }

    accept() {
        window.location = this.state.accept_url;
    }

    reject() {
        axios.get(this.state.reject_url);
        this.setState({
            status: 'none',
            partner_id: 0,
            partner_name: '',
            partner_avatar: '',
            end_url: '',
            accept_url: '',
            reject_url: ''
        })
    }

    componentDidMount() {
        Echo.private('Dating.' + this.props.userid)
            .listen('.Call', (e) => {
                this.setStatus('incomingcall', e.from.id, e.from.person.name, e.from.person.avatar, '', e.end_url, e.accept_url, e.reject_url)
            })
            .listen('.CallReceive', (e) => {
                if(this.state.cron_call){
                    clearInterval(this.state.cron_call);
                }
            })
            .listen('.CallEnd', (e) => {
                this.setState({
                    status: 'none',
                    partner_id: 0,
                    partner_name: '',
                    partner_avatar: '',
                    end_url: '',
                    accept_url: '',
                    reject_url: ''
                })
            })
            .listen('.CallAccept', (e) => {
                if(this.state.cron_call){
                    clearInterval(this.state.cron_call);
                }
                window.location.href = e.room_url;
            })
            .listen('.CallReject', (e) => {
                if(this.state.cron_call){
                    clearInterval(this.state.cron_call);
                }
                this.setState({
                    status: 'none',
                    partner_id: 0,
                    partner_name: '',
                    partner_avatar: '',
                    end_url: '',
                    accept_url: '',
                    reject_url: '',
                    cron_call: null
                })
            });
    }

    render() {
        return (
            <Dialog
                open={this.state.status != 'none'}
                className={'call-container'}
                BackdropProps={{
                    styles: {
                     root: styles.root,
                     zIndex: 1200000
                    }
                }}
                  PaperProps ={{
                    styles: {
                     root: styles.paper
                    }
                }}
                style={{overlay: {zIndex: 1200000}}}
                >
                <DialogContent>
                    <div className="call-container">
                        {this.state.status != 'none' &&
                            <div className="avatar">
                                <img src={this.state.partner_avatar}/>
                                <h3>{this.state.partner_name}</h3>
                            </div>
                        }

                        {this.state.status == 'call' &&
                            <div className="text-center">
                                <div className="reject" onClick={this.end}>
                                    <i className="fa fa-phone" aria-hidden="true"></i>
                                </div>
                            </div>
                        }

                        {this.state.status == 'incomingcall' &&
                            <div className="text-center">
                                <div className="d-inline mr-4">
                                    <div className="accept" onClick={this.accept}>
                                        <i className="fa fa-phone" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <div className="d-inline">
                                    <div className="reject" onClick={this.reject}>
                                        <i className="fa fa-phone" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        }
                    </div>
                </DialogContent>
            </Dialog>
        );
    }
}

var component = document.getElementById('videocall');
if(component){
    var props = Object.assign({}, component.dataset);

    ReactDOM.render(
        <VideoCall {...props} ref={(videocallComponent) => {window.videocallComponent = videocallComponent}} />
        , component);
}
