let iconCancel = document.querySelectorAll('.cancel');
let infor = document.getElementById('info');
let thongKe = document.getElementById('thongKe');
let listItem = document.querySelectorAll('.list-item');

iconCancel[0].onclick = function () {
    infor.style.display = 'none';
};
iconCancel[1].onclick = function () {
    thongKe.style.display = 'none';
}
for (const item of listItem) {
    let listChild = item.nextElementSibling
    item.onclick = function () {
        let display = getComputedStyle(listChild).display;
        if (display == 'block'){
            listChild.style.display = 'none';
        }
        else{
            listChild.style.display = 'block';
        }
          
    }
}
