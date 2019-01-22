import React from 'react'
import loadingBar from "../image/loading_bar.svg";
export default class GalleryItem extends React.Component{
    constructor(props) {
        super(props);
        this.myRef = React.createRef();
        this.state = {
            isObserved: false,
            isLoaded: false
        };
    }

    componentDidMount() {
        let yOffset = this.myRef.current.getBoundingClientRect().y;
        let windowYOffset = window.scrollY + window.innerHeight;
        if (windowYOffset > yOffset) {
            this.setState({
                isObserved: true
            })
        } else {
            let observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entries.isIntersecting === true || entry.intersectionRatio > 0) {
                        console.log(entry);
                        this.setState({
                            isObserved: true
                        });
                        observer.unobserve(entry.target);
                    }
                })
            });
            observer.observe(this.myRef.current);
        }
    }

    render() {
        return (
            <div className={"gallery-item"} ref={this.myRef}>
                <div className={"gallery-item__img-container hoverable"} onClick={() => {
                    this.props.onClick(this.props.id)
                }}>
                    <img className={this.state.isLoaded ? "gallery-item__img-loader invisible" : "gallery-item__img-loader visible"} src={loadingBar}/>
                    <img className={"gallery-item__img"} src={this.state.isObserved ? this.props.thumbnail : null} onLoad={() => {
                        this.setState({
                            isLoaded: true
                        })
                    }}/>
                </div>
                <div className={"gallery-item__body"}>
                    <div className={"gallery-item__header text-primary"}>
                        {this.props.header ? this.props.header : "Untitled"}
                    </div>
                    <div className={"gallery-item__content row"}>
                        <div className={"col text-secondary"}>
                            <small><i>Creator: {this.props.owner ? this.props.owner : "Anonymous"}</i></small>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}