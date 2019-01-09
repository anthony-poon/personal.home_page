import React from "react"

export default class GalleryViewer extends React.Component{
    constructor(props) {
        super(props)
    }

    render() {
        return (
            <div className={"gallery-viewer col"}>
                <div className={"row h-100"}>
                    <div className={"gallery-viewer__img-wrapper col p-0 d-flex justify-content-center align-items-center"}>
                        <img src={this.props.asset}/>
                    </div>
                    <div className={"gallery-viewer__body col-lg-3 col-md-4 col-12"}>
                        <div className={"d-flex flex-column px-3 h-100 py-4"}>
                            <div className={"gallery-viewer__header flex-shrink-0 row"}>
                                <div className={"col"}>
                                    {this.props.header}
                                </div>
                                <div className={"col-auto"}>
                                    <button
                                        type="button"
                                        className={"gallery-viewer__close-btn btn btn-outline-primary btn-sm border-0"}
                                        onClick={() => {
                                            this.props.onClose()
                                        }}>
                                        <i className="fas fa-times"/>
                                    </button>
                                </div>
                            </div>
                            <div className={"gallery-viewer__content flex-grow-1 my-3"}>
                                {this.props.content}
                            </div>
                            <div className={"gallery-viewer__btn-bar d-flex flex-shrink-0"}>
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
        )
    }
}