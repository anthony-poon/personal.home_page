import React from 'react'
import axios from 'axios';
import GalleryItem from './GalleryItem';
import "./GalleryUpload";
import GalleryUpload from "./GalleryUpload";
import GalleryModal from  "./GalleryModal";
import GalleryViewer from "./GalleryViewer";

export default class GalleryApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            modal: {
                isVisible: false,
                isLoaded: true,
                content: null
            }
        };
    }

    expandItem(item) {
        history.pushState(null, null, "/gallery/" + item.id);
        this.setState({
            modal: {
                isVisible: true,
                content: (
                    <GalleryViewer
                        id={item.id}
                        header={item.header}
                        content={item.content}
                        assets={item.assets}
                    />
                )
            }
        })
    };

    componentDidMount() {
        axios.get(this.props.ajaxUrl).then((res) => {
            if (200 === res.status) {
                let items = $.map(res.data, (v, k) => {
                    return (
                        <div className={"gallery-item-wrapper"} key={v.id}>
                            <GalleryItem
                                 id={v.id}
                                 header={v.header}
                                 content={v.content}
                                 assets={v.assets}
                                 onClick={() => {
                                     this.expandItem(v)
                                 }}
                            />
                        </div>
                    )});
                this.setState({
                    items: items,
                });
            } else {
                console.error(res)
            }
        });

        let url = window.location.pathname;
        let reg = /^\/gallery\/?(\d+)(.*)$/
        let match = reg.exec(url);
        if (match && match[1]) {
            history.replaceState(null, null, "/gallery");
            this.expandItem(match[1]);
        }
    };

    render() {
        return (
            <div className={"gallery-app"}>
                <div className={"container py-5"}>
                    <div className="row">
                        {this.state.items}
                    </div>

                </div>
                <div className={"gallery-app__upload-button"}>
                    <GalleryUpload
                        ajaxUrl={this.props.ajaxUrl}
                    />
                </div>
                <div className={this.state.modal.isVisible ? "gallery-app__modal" : "gallery-app__modal d-none"}>
                    <GalleryModal
                        content={this.state.modal.content}
                        isVisible={this.state.modal.isVisible}
                    />
                </div>
            </div>
        );
    }
}

