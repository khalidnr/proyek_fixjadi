# JANLUP DOKUMENTASI
## Dependencies
> [!IMPORTANT]
> Sebelum Mulai, Instal beberapa paket dibawah terlebih dahulu
* apache2
```
sudo apt install apache2
```
* mySQL
```
sudo apt install mysql-server
```
* php
```
sudo apt install php
sudo apt install php-mysql
```
* Docker Engine
1. Instal preReq
```
# Add Docker's official GPG key:
sudo apt-get update
sudo apt-get install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
```
  2. Instal Docker
```
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```
  3. Test Docker
```
sudo docker run hello-world
```

## BUILD
### Script
Instal npm
```
sudo apt install npm
```
Instal front dependencies npm
```
npm install bootstrap@5 jquery@3
```
Inisialisasi npm
```
npm init -y
```
di dalam <b>package.json</b>, masukkan baris kode ini ke dalam bagian "scripts":
```
"build": "mkdir -p public/css public/js && cp node_modules/bootstrap/dist/css/bootstrap.min.css public/css/ && cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js public/js/ && cp node_modules/jquery/dist/jquery.min.js public/js/",
"start": "npm run build && echo 'Project built! Run your PHP server manually'"
```
Masukkan kode ini kedalam bagian header pada setiap php yang mengandung html
```
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="/css/bootstrap.min.css">
```
Masukkan kode ini kedalam bagian body pada setiap php yang mengandung html
```
<!-- jQuery & Bootstrap JS -->
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
```
Build
```
npm run build
```
### Konfigurasi mysqli-connect
buka file <b>php.ini</b> dengan privilege root (disarankan pakai aplikasi text editor seperti sublime-text)
```
# Pilih salah satu, tergantung versi php yang terinstal
sudo subl /etc/php/8.1/cli/php.ini
sudo subl /etc/php/8.3/cli/php.ini
```
Carilah baris kode *;extension=mysqli* (pada kasus ini, berada di line 930) lalu hapus komentar (;) yang berada di depan kode tersebut, sehingga hasil akhir akan terlihat seperti ini
```
;extension=mbstring
;extension=exif      ; Must be after mbstring as it depends on it
extension=mysqli
;extension=oci8_12c  ; Use with Oracle Database 12c Instant Client
;extension=oci8_19  ; Use with Oracle Database 19 Instant Client
```
Restart apache2 service
```
systemctl restart apache2
```
### Konfigurasi hak akses session
Defaultnya, session akan tersimpan di
```
/var/lib/php/sessions
```
Ubah hak akses direktori tersebut menggunakan command
```
sudo chown -R www-data:www-data /var/lib/php/sessions
```
### Start 
Start aplikasi
```
sudo php -S localhost:8000
```
Pindah ke http://localhost:8000

## Test
Instal OWASP ZAP nya gimana
```

```

## Release
### Instal Jenkins lewat docker
Bridge network
```
sudo docker network create jenkins
```
Download dan run `docker:dind`
```
sudo docker run \
  --name jenkins-docker \
  --rm \
  --detach \
  --privileged \
  --network jenkins \
  --network-alias docker \
  --env DOCKER_TLS_CERTDIR=/certs \
  --volume jenkins-docker-certs:/certs/client \
  --volume jenkins-data:/var/jenkins_home \
  --publish 2376:2376 \
  docker:dind \
  --storage-driver overlay2
```
buat **Dockerfile** dengan konten:
```
FROM jenkins/jenkins:2.504.2-jdk21
USER root
RUN apt-get update && apt-get install -y lsb-release ca-certificates curl && \
    install -m 0755 -d /etc/apt/keyrings && \
    curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc && \
    chmod a+r /etc/apt/keyrings/docker.asc && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
    https://download.docker.com/linux/debian $(. /etc/os-release && echo \"$VERSION_CODENAME\") stable" \
    | tee /etc/apt/sources.list.d/docker.list > /dev/null && \
    apt-get update && apt-get install -y docker-ce-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*
USER jenkins
RUN jenkins-plugin-cli --plugins "blueocean docker-workflow json-path-api"
```
Build
```
docker build -t myjenkins-blueocean:2.504.2-1 .
```
Run
```
docker run \
  --name jenkins-blueocean \
  --restart=on-failure \
  --detach \
  --network jenkins \
  --env DOCKER_HOST=tcp://docker:2376 \
  --env DOCKER_CERT_PATH=/certs/client \
  --env DOCKER_TLS_VERIFY=1 \
  --publish 8080:8080 \
  --publish 50000:50000 \
  --volume jenkins-data:/var/jenkins_home \
  --volume jenkins-docker-certs:/certs/client:ro \
  myjenkins-blueocean:2.504.2-1
```

### Konfigurasi Jenkins
Cloned Repository Ke Local
```
git clone https://github.com/meyudha/project.git
```
Navigasi ke Cloned Directory
```
cd project
```
Menjalankan jenkins dengan docker compose
```
git clone https://github.com/jenkins-docs/quickstart-tutorials.git
```
Navigasi ke quickstart-tutorials dan jalankan command ini
```
cd quickstart-tutorials
docker-compose --profile node up -d
```
Akses http://localhost:8080  
> [!NOTE]
> lihat password dengan menggunakan command `docker logs <container_id>`  
> Container id docker adalah text berformat hex rubbish setelah run jenkins

## Deploy
### Membuat Job Baru 
- Pada UI Jenkins, Klik **New Item** pada sidebar kiri
- Pilih **pipeline** dan berikan nama job "Project"
- Klik **Ok**
### Konfigurasi Pipeline 
- Cari Bagian **Pipeline** Section
- Dibawah **Definition**, Pilih **Pipeline script from SCM**
- Di **SCM**, Pilih **Git** dan tambahkan Repository URL
- Pastikan Branch specifier sesuai dengan branch pada github (pada kasus ini branch ada di main)
```
https://github.com/meyudha/project.git
```
- Save Job
### Update isi dari Jenkinsfile
```
pipeline {
    agent {
        docker {
            image 'php:8.2-cli'
            args '-u root'
        }
    }
    
    tools {
        nodejs 'nodejs-18' // Name from Global Tools config
    }
    
    environment {
        DEPLOY_TARGET = '/var/www/your-project'
    }
    
    stages {
        stage('Setup Environment') {
            steps {
                sh '''
                    echo "=== Setting up Environment ==="
                    
                    # Install required packages
                    apt-get update
                    apt-get install -y git unzip curl nodejs npm
                    
                    # Install Composer
                    curl -sS https://getcomposer.org/installer | php
                    mv composer.phar /usr/local/bin/composer
                    chmod +x /usr/local/bin/composer
                    
                    # Verify installations
                    php --version
                    composer --version
                    node --version
                    npm --version
                '''
            }
        }
        
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/meyudha/Project.git'
            }
        }
        
        stage('Install Dependencies') {
            steps {
                sh '''
                    echo "=== Installing PHP Dependencies ==="
                    composer install --no-interaction --optimize-autoloader
                    
                    echo "=== Installing Node Dependencies ==="
                    npm ci
                '''
            }
        }
        
        stage('Build Assets') {
            steps {
                sh '''
                    echo "=== Building Assets ==="
                    npm run build
                '''
            }
        }
        
        stage('Run Tests') {
            steps {
                script {
                    try {
                        sh '''
                            echo "=== Running PHP Tests ==="
                            if [ -f "vendor/bin/phpunit" ]; then
                                vendor/bin/phpunit
                            else
                                echo "PHPUnit not found, skipping PHP tests"
                            fi
                        '''
                    } catch (Exception e) {
                        echo "PHP tests failed: ${e.getMessage()}"
                    }
                    
                    try {
                        sh '''
                            echo "=== Running JavaScript Tests ==="
                            if npm list --json | grep -q '"test"'; then
                                npm test
                            else
                                echo "No npm test script found, skipping JS tests"
                            fi
                        '''
                    } catch (Exception e) {
                        echo "JavaScript tests failed: ${e.getMessage()}"
                    }
                }
            }
        }
        
        stage('Deploy') {
            steps {
                sh '''
                    echo "=== Deployment Stage ==="
                    echo "Project built successfully"
                    echo "Files ready for deployment to: ${DEPLOY_TARGET}"
                '''
            }
        }
    }
    
    post {
        always {
            echo 'Pipeline execution completed'
        }
        success {
            echo 'Pipeline executed successfully!'
        }
        failure {
            echo 'Pipeline failed. Check the logs above for details.'
        }
    }
}
```
### Hasil Setelah Dilakukan Deploy
![image](https://github.com/user-attachments/assets/7b17a7dd-a0ec-4a7d-9321-c72fd8baaeb7)


## Operate
Instal dockernya gimana
```

```

## Monitor
Buat user untuk Prometheus
```
sudo useradd --no-create-home --shell /bin/false prometheus
```
Buat direktori untuk Prometheus
```
sudo mkdir /etc/prometheus
sudo mkdir /var/lib/prometheus
```
Set ownership
```
sudo chown prometheus:prometheus /etc/prometheus
sudo chown prometheus:prometheus /var/lib/prometheus
```
Download Prometheus
```
cd /tmp
wget https://github.com/prometheus/prometheus/releases/download/v2.40.0/prometheus-2.40.0.linux-amd64.tar.gz
```
Extract file
```
tar xvf prometheus-2.40.0.linux-amd64.tar.gz
cd prometheus-2.40.0.linux-amd64
```
Buat file konfigurasi prometheus.yml
```
```
Buat service file
```
```
Reload systemd
```
sudo systemctl daemon-reload
```
Start prometheus
```
sudo systemctl start prometheus
```
Enable auto start
```
sudo systemctl enable prometheus
```
