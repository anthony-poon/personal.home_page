import React from "react"

export default class GalleryViewer extends React.Component{
    constructor(props) {
        super(props)
    }
    render() {

        return (
            <div className={"gallery-viewer"}>
                <div className={"gallery-viewer__img-wrapper"}>
                    <img src={this.props.assets[0]}/>
                </div>
                <div className={"gallery-viewer__body"}>
                    <div className={"gallery-viewer__header row"}>
                        <div className={"col"}>
                            {this.props.header}
                        </div>
                        <div className={"col-auto"}>
                            <button
                                type="button"
                                className={"gallery-viewer__close-btn btn btn-outline-primary btn-sm border-0"}
                                onClick={() => {
                                    this.btnClose()
                                }}>
                                <i className="fas fa-times"/>
                            </button>
                        </div>
                    </div>
                    <div className={"gallery-viewer__content"}>
                        {this.props.content}
                    </div>
                    <div className={"gallery-viewer__btn-bar"}>
                        <button className={"btn btn-outline-primary btn-sm border-0"} type={"button"}>
                            <i className="far fa-heart"/>
                        </button>
                        <button type={"button"} className="btn btn-outline-primary btn-sm border-0">
                            <i className="fas fa-share-alt"/>
                        </button>
                    </div>
                </div>
            </div>
        )
    }
}