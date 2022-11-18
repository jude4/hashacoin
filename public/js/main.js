console.log('This is working')

document.getElementsByTagName('head')[0].appendChild(
    Object.assign(document.createElement('link'), {
        rel:'stylesheet',
        href:'main.css', //I will add this to github and supply the link
        // here instead
    }),
)



/*
    GLOBALS
 */
window.spinner = '';



/*
 Utility functions
 */
function setAttributes(el, attrs) {
    for (var key in attrs) {
        el.setAttribute(key, attrs[key]);
    }
}

function css(element, style) {
    for (const property in style) {
        element.style[property] = style[property];
    }
}


/*
    Creates an element
 */

const buildDom = (tag,style,data,attrs)=>{
    const ele = document.createElement(tag)
   Object.assign(ele, {
       style: style,
       innerHTML: data,
   })
    setAttributes(ele, attrs)
    return ele;
}

const createModal = ()=> {
    //span close icon
    const spanClose = document.createElement('span')
    spanClose.innerHTML = '<strong>&times;</strong>'
    spanClose.className = 'sparkle-close'
    const headerTitle = document.createElement('h2')
    headerTitle.textContent = 'Modal Header'

    //close button
    const closeBtn = buildDom('button','','Close', {'id':'closeButton'})
    closeBtn.classList = 'sparkle-btn sparkle-btn-secondary'
    //Create the sparkle-modal container
    const modal = buildDom('div','', '',{'class':'sparkle-modal'})
//create the sparkle-modal content
    const modalContent = buildDom('div','','',{'class':'sparkle-modal-content'})
//create the sparkle-modal header
    const modalHeader = buildDom('div','', '')
    modalHeader.className = 'sparkle-modal-header'
    //append the spanclose to the sparkle-modal header
    modalHeader.append(spanClose,headerTitle)
//create the sparkle-modal body
    const modalBody = buildDom('div','','<p>Some text in the Modal Body</p><p>Some other text...</p>')
    modalBody.className = 'sparkle-modal-body'
//create the sparkle-modal footer
    const modalFooter = buildDom('div','','',{})
    modalFooter.className = 'sparkle-modal-footer'
    modalFooter.append(closeBtn)

    //append the header,body and footer to the sparkle-modal contwnt
    modalContent.append(modalHeader,modalBody,modalFooter)
    //append the sparkle-modal content to the sparkle-modal
    modal.appendChild(modalContent)
    //append the sparkle-modal to the body
    document.body.appendChild(modal)

    //closes the sparkle-modal when close button is clicked
    closeBtn.addEventListener('click', ()=>{
        console.log('This button is working ')
        modal.style.display = 'none';
    })

    spanClose.addEventListener('click', ()=>{
        console.log('This button is working ')
        modal.style.display = 'none';
    })

    // When the user clicks anywhere outside of the sparkle-modal, close it
// window.onclick = function(event) {
//     if (event.target == sparkle-modal) {
//         sparkle-modal.style.display = "none";
//     }
// }
    return modal
}

//show the sparkle-modal
function showModal() {
    //get the sparkle-modal
    let myModal = createModal()
    myModal.style.display = 'block'
}
//show a spinner
function showSpinner() {
    window.spinner = document.createElement('div')
    window.spinner.setAttribute('id','my-spinner')
    window.spinner.className = 'sparkle-spinner-border';
    document.body.appendChild(window.spinner)
    console.log(window.spinner.parentNode)
}

//hides the spinner
function hideSpinner() {
   //get the spinner
    if (document.body.contains(document.getElementById('my-spinner'))){
        document.body.removeChild(document.getElementById('my-spinner'))
        console.log('Yes')
    }
    else{
        console.log('No')
    }
}

//show spinner
const spinBtn = document.getElementById('showSpin')
spinBtn.addEventListener('click', ()=>{
    showSpinner()
})

//hide the spinner
document.getElementById('hideSpin').addEventListener('click', ()=>{
    hideSpinner()
})

//Open the sparkle-modal
const button = document.querySelector('button')
console.log(button)
button.addEventListener('click', showModal)


