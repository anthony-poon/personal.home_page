import React from "react";
import axios from "axios";

export default class GalleryModal extends React.Component{
    constructor(props) {
        super(props);
        this.state = {
            id: null,
            header: "",
            content: "",
            assets: [],
            isVisible: false,
            isLoaded: false
        }
    }
    expandItem(url) {
        let reg = /^\/gallery\/?(\d+)(.*)$/
        let match = reg.exec(url);
        if (match && match[1]) {
            let ajaxUrl = this.props.ajaxUrl + "/" + match[1];
            this.setState({
                isVisible: true
            });
            axios.get(ajaxUrl).then((res) => {
                if (200 === res.status) {
                    this.setState({
                        id: res.data.id,
                        header: res.data.header,
                        content: res.data.content,
                        assets: res.data.assets,
                        isLoaded: true
                    });
                } else {
                    console.error(res)
                }
            })
        }
    }
    componentDidMount() {
        $(window).on("popstate", (evt) => {
            let stateObj = evt.originalEvent.state;
            if (stateObj == null) {
                this.setState({
                    isVisible: false,
                    isLoaded: false,
                })
            }
        });
        $(window).on("pushstate", (evt) => {
            this.expandItem(window.location.pathname);
        });
        this.expandItem(window.location.pathname);
    }
    btnClose() {
        this.setState({
            isVisible: false,
            isLoaded: false,
        })
    }

    render() {
        if (this.state.isVisible) {
            $("body").css("overflow-y", "hidden");
        } else {
            $("body").css("overflow-y", "auto");
        }
        return (
            <div className={this.state.isVisible ? "gallery-modal" : " gallery-modal d-none"}>
                <div className={"gallery-modal__dialog"}>
                    <div className={"gallery-dialog"}>
                        <div className={"gallery-dialog__img-wrapper"}>
                            {this.state.isLoaded ? (
                                <img src={this.state.assets[0]}/>
                            ) : (
                                <div className="lds-ring">
                                    <div/>
                                    <div/>
                                    <div/>
                                    <div/>
                                </div>
                            )}
                        </div>
                        <div className={"gallery-dialog__body"}>
                            <div className={this.state.isLoaded ? "gallery-body__header row" : "gallery-body__header row d-none"}>
                                <div className={"col"}>
                                    {this.state.header}
                                </div>
                                <div className={"col-auto"}>
                                    <button
                                        type="button"
                                        className={"gallery-modal__close-btn btn btn-outline-primary btn-sm border-0"}
                                        onClick={() => {
                                            this.btnClose()
                                        }}>
                                        <i className="fas fa-times"/>
                                    </button>
                                </div>

                            </div>
                            <div className={this.state.isLoaded ? "gallery-body__content" : "gallery-body__content d-none"}>
                                {this.state.content}
                            </div>
                            <div className={this.state.isLoaded ? "gallery-body__btn-bar" : "gallery-body__btn-bar d-none"}>
                                <button className={"btn btn-outline-primary btn-sm border-0"} type={"button"}>
                                    <i className="far fa-heart"/>
                                </button>
                                <button type={"button"} className="btn btn-outline-primary btn-sm border-0">
                                    <i className="fas fa-share-alt"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
};