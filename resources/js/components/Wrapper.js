import {React, useEffect} from "react";
import ReactDOM from "react-dom";

export default function Wrapper(){

    useEffect(()=>{
        showCommencePaymentModal();
    }, [])

    const showCommencePaymentModal = ()=>{
        let commencePaymentModal = new Modal(document.getElementById('paymentBeginsModal'))
        commencePaymentModal.show();
    }
    const showSecondModal = ()=>{
        let secondModal = new Modal(document.getElementById('secondModal'))
        //get the id of the first modal
        let paymentModal = document.getElementById('paymentBeginsModal')
        //get the modal instance
        let paymentModalInstance = Modal.getInstance(paymentModal)
        paymentModalInstance.hide()
        secondModal.show()
    }

    return (
        <>
            {/*Commence Payment Modal*/}
            <div className="modal" tabIndex="-1" id={'commence-payment'} data-bs-backdrop="static" data-bs-keyboard="false">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">Modal title</h5>
                            <button type="button" className="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <p>Payment modal goes here.</p>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary"
                                    data-bs-dismiss="modal">Close
                            </button>
                            <button onClick={()=> showSecondModal()} type="button"
                                    className="btn btn-primary">Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            {/*First Step modal*/}
            <div className="modal" tabIndex="-1" id={'secondModal'}>
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">Modal title</h5>
                            <button type="button" className="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div className="modal-body">
                            <p>Second Modal  goes here.</p>
                        </div>
                        <div className="modal-footer">
                            <button type="button" className="btn btn-secondary"
                                    data-bs-dismiss="modal">Close
                            </button>
                            <button type="button"
                                    className="btn btn-primary">Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
if(document.getElementById('wrapper')){
    ReactDOM.render(<Wrapper/>, document.getElementById('wrapper'))
}

