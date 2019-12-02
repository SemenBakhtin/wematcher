import React, { Component } from 'react'
import { css } from '@emotion/core';
import './Searching.scss';
import CircleLoader from 'react-spinners/CircleLoader';

export default class Searching extends Component {
    render() {
        const spinnerCss = css`
            margin-left: auto;
            margin-right: auto;
            margin-top: 40px;
        `;
        const {t} = this.props
        return (
            <div className="loading">
                <div className="videowrapper">
                    <video id="welcomeLocalVideo" autoPlay></video>
                    <div className="control-box">
                        <div className="loading_icon">
                            <CircleLoader css={spinnerCss} sizeUnit={"px"} size={60} color={'white'}/>
                        </div>
                        <h3 className="mt-4 text-white">{t("finding new partner......")}</h3>
                        <button className="common-btn primary shadow" onClick={this.props.action}>{t("Stop searching")}</button>
                        <br/>
                        <a href={this.props.genderurl} className="text-white"><i className="fa fa-search" aria-hidden="true"></i> {t("Interested in")}</a>
                    </div>
                </div>
            </div>
        );
    }
}
