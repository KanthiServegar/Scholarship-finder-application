import sys
from PyQt5.QtWidgets import QApplication, QMainWindow, QVBoxLayout, QWidget
from PyQt5.QtWebEngineWidgets import QWebEngineView
from PyQt5.QtCore import QUrl

class PixFusionApp(QMainWindow):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("PixFusion")
        self.setGeometry(100, 100, 1200, 800)

        # Set up the central widget
        self.central_widget = QWidget()
        self.setCentralWidget(self.central_widget)

        # Set up the layout
        layout = QVBoxLayout()
        self.central_widget.setLayout(layout)

        # Set up QWebEngineView
        self.browser = QWebEngineView()
        # Update this to your PHP server URL
        php_server_url = "http://localhost/login%20system%20with%20avatar/home.php"
        self.browser.setUrl(QUrl(php_server_url))
        layout.addWidget(self.browser)

if __name__ == "__main__":
    app = QApplication(sys.argv)
    window = PixFusionApp()
    window.show()
    sys.exit(app.exec_())
