import React, { Component } from 'react'
import SearchButton from '../SearchButton/SearchButton';
import './CheckDevice.scss';

export default class CheckDevice extends Component {
    render() {
        const {t} = this.props
        return (
            <div className="checkdevice">
                <div className="videowrapper">
                    <video id="welcomeLocalVideo" autoPlay></video>
                    <div className="control-box">
                        <h2 className="mt-3 text-white">{t("Find someone to chat with!")}</h2>
                        <SearchButton {...this.props} action={this.props.action} />
                        <br/>
                        <a href={this.props.genderurl} className="text-white"><i className="fa fa-search" aria-hidden="true"></i> {t("Interested in")}</a>
                    </div>
                </div>
            </div>
        );
    }
}
