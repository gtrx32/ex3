BX.ready(function () {
    const isFrame = BX.SidePanel && BX.SidePanel.Instance && BX.SidePanel.Instance.isOpen();

    if (!isFrame) {
        BX.SidePanel.Instance.open(location.href, {
            width: 1500,
            label: {
                text: "Элемент",
            },
            events: {
                onClose: function () {
                    location.href = '/exam31/';
                }
            }
        });
    }
});