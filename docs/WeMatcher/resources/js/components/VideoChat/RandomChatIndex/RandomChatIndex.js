import React, { Component } from 'react'
import SearchButton from '../SearchButton/SearchButton';
import PhotoCarousel from '../../PhotoCarousel/PhotoCarousel';

export default class RandomChatIndex extends Component {
    render() {
        const {t} = this.props
        return (
            <div>
                <div className="row justify-content-center">
                    <div className="col-md-12">
                        <PhotoCarousel urls={this.props.photourls}/>
                    </div>
                </div>
                <div className="row justify-content-center mt-4">
                    <div className="col-md-12 text-center">
                        <h2>{t("Want to find someone to chat with?")}</h2>
                        <div>
                        <SearchButton {...this.props} action={this.props.action} />
                        </div>
                        <i className="fa fa-video-camera" aria-hidden="true"></i> {t("Activate your camera to start searching")}
                        <br/>
                        <a href={this.props.genderurl}><i className="fa fa-search" aria-hidden="true"></i> {t("Interested in")}</a>
                    </div>
                </div>
            </div>
        );
    }
}
