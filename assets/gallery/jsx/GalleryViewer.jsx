import React from "react"
import GalleryNav from "./GalleryNav";
import axios from "axios";
import BtnLikeText from "./Component/BtnLikeText"
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faHeart as farHeart } from "@fortawesome/free-regular-svg-icons"
import {faHeart as fasHeart, faShareAlt, faTrashAlt} from "@fortawesome/free-solid-svg-icons"
import loadingBar from "../image/loading_bar.svg";
export default class GalleryViewer extends React.Component{
    constructor(props) {
        super(props);
        // far is empty heard, fas is full
        this.state = {
            isLiked: this.props.isLiked,
            likeBtn: {
                content: this.props.isLiked ? <FontAwesomeIcon icon={fasHeart}/> : <FontAwesomeIcon icon={farHeart}/>,
                onClick: () => { return this.onLikeBtnClick() }
            },
            shareBtn: {
                content: <FontAwesomeIcon icon={faShareAlt}/>,
                onClick: () => { console.log("clicked") }
            },
            imageIndex: 0,
            imageStack: null,
            imageSrc: null,
            isLoaded: false
        }
    }

    async onLikeBtnClick() {
        let response;
        if (this.props.isLoggedIn) {
            if (this.state.isLiked) {
                response = await axios.request({
                    method: "DELETE",
                    url: this.props.ajaxUrl + "/" + this.props.id + "/likes",
                });
                if (200 === response.status) {
                    this.setState({
                        isLiked: false,
                        likeBtn: {
                            ...this.state.likeBtn,
                            content: <FontAwesomeIcon icon={farHeart}/>
                        }
                    });
                    return response;
                } else {
                    return Promise.reject(response);
                }
            } else {
                response = await axios.request({
                    method: "POST",
                    url: this.props.ajaxUrl + "/" + this.props.id + "/likes",
                });
                if (200 === response.status) {
                    this.setState({
                        isLiked: false,
                        likeBtn: {
                            ...this.state.likeBtn,
                            content: <FontAwesomeIcon icon={fasHeart}/>
                        }
                    });
                    return response;
                } else {
                    return Promise.reject(response);
                }
            }
        } else {
            this.setState({
                likeBtn: {
                    content: <span>Sign In</span>,
                    onClick: () => {
                        window.location.href = this.props.signInLink
                    }
                },
            })
        }
    }

    cycleImages() {
        if (this.props.assets.length > 1) {
            this.setState((prevState) => {
                if (prevState.imageIndex + 1 >= this.props.assets.length) {
                    return {
                        isLoaded: false,
                        imageIndex: 0,
                        imageSrc: this.props.assets[0]
                    };
                } else {
                    console.log(prevState.imageIndex + 1);
                    return {
                        isLoaded: false,
                        imageIndex: prevState.imageIndex + 1,
                        imageSrc: this.props.assets[prevState.imageIndex + 1]
                    };
                }
            })
        }
    }

    onLoadingBarLoaded() {
        console.log("Loading bar loaded");
        this.setState({
            imageIndex: 0,
            imageSrc: this.props.assets[0]
        });
    }

    onImageLoad() {
        console.log("Image loaded");
        this.setState({
            isLoaded: true,
        });
    }

    async onDelete() {
        let response = await axios.delete(this.props.ajaxUrl + "/" + this.props.id);
        if (200 === response.status) {
            // reload gallery
            this.props.this.props.modalClose();
            return response;
        } else {
            return Promise.reject(response);
        }
    }

    render() {
        return (
            <div className={"row"}>
                <div className={"col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4 mx-auto"}>
                    <div className={"gallery-viewer"}>
                        <GalleryNav
                            onCloseBtnClick={this.props.modalClose}
                            header={this.props.header ? this.props.header : "Untitled"}
                            actionBar={(
                                <div className={"d-flex align-items-center text-secondary"}>
                                    {this.state.imageIndex + 1} of {this.props.assets.length}
                                </div>
                            )}
                        />
                        <div className={"gallery-viewer__body"}>
                            <div id={"js-image-container"} className={ this.props.assets.length > 1 ? "gallery-viewer__image hoverable" : "gallery-viewer__image" } onClick={() => {
                                this.cycleImages();
                            }}>
                                <img className={this.state.isLoaded ? "invisible loader" : "visible loader"} src={loadingBar} onLoad={() => {
                                    this.onLoadingBarLoaded();
                                }}/>
                                <img className={this.state.isLoaded  ? "visible" : "invisible"}
                                     src={this.state.imageSrc} onLoad={this.onImageLoad.bind(this)}
                                />
                            </div>
                            <div className={"gallery-viewer__content px-3 py-3"}>
                                <div className={"row"}>
                                    <div className={"col"}>
                                        <h5 className="text-primary">
                                            {this.props.header ? this.props.header : "Untitled"}
                                        </h5>
                                    </div>
                                    <div className={"col-auto"}>
                                        <div className={"gallery-viewer__btn_bar"} style={{
                                            marginLeft: "-4px",
                                            marginRight: "-4px"
                                        }}>
                                            <div className={"d-flex align-items-center"}>
                                                <BtnLikeText
                                                    onClick={this.state.likeBtn.onClick}
                                                    content={this.state.likeBtn.content}
                                                />
                                                <BtnLikeText
                                                    onClick={this.state.shareBtn.onClick}
                                                    content={this.state.shareBtn.content}
                                                />
                                                {this.props.canDelete ?
                                                    (<BtnLikeText
                                                        onClick={this.onDelete.bind(this)}
                                                        colorClass={"text-danger"}
                                                        content={<FontAwesomeIcon icon={faTrashAlt} fixedWidth />}
                                                    />) : null}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div className={"row"}>
                                    <p className={"col text-secondary"}>
                                        {this.props.content}
                                    </p>
                                </div>
                                <div className={"row"}>
                                    <div className={"col text-secondary"}>
                                        <small><i>Posted By: {this.props.owner ? this.props.owner : "Anonymous"}</i></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}