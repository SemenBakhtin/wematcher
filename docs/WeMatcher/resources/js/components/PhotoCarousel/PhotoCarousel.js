import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Carousel from 'react-multi-carousel';
import 'react-multi-carousel/lib/styles.css';
import './PhotoCarousel.css';

export default class PhotoCarousel extends Component {
    render() {
        const urls = this.props.urls;
        const listItems = urls.map((url, index) =>
            <div key={index}>
                <img src={url}/>
            </div>
        );

        const responsive = {
            superLargeDesktop: {
              // the naming can be any, depends on you.
              breakpoint: { max: 4000, min: 3000 },
              items: 5,
            },
            desktop: {
              breakpoint: { max: 3000, min: 0 },
              items: 3,
            },
        };

        return (
            <Carousel 
                swipeable={false}
                draggable={false}
                arrows={false}
                showDots={false}
                responsive={responsive}
                ssr={true} // means to render carousel on server-side.
                infinite={true}
                autoPlay={true}
                keyBoardControl={true}
                customTransition="all 1s linear"
                transitionDuration={1000}
                containerClass="carousel-container"
                deviceType={this.props.deviceType}
                partialVisbile={true}
                itemClass="photo_carousel_item">
                {listItems}
            </Carousel>
        );
    }
}
