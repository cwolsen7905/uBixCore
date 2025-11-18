#!/usr/bin/bash

# Check if environment is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <environment>"
    exit 1
fi

ENVIRONMENT=$1

DATE_STAMP=`date +'%s'`

KUBECTL="kubectl"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"

# If environment is prod use la3config and la3config-blue as array otherwise use devstageconfig
if [ "$ENVIRONMENT" == "main" ]; then
	KUBECONFIGS=("/home/gitlab-runner/.kube/la3config" "/home/gitlab-runner/.kube/la3config-blue")
elif [ "$ENVIRONMENT" == "sandbox" ]; then
    if [ ! -d $SCRIPT_DIR/log ]; then
        mkdir $SCRIPT_DIR/log
    fi
    chmod 777 $SCRIPT_DIR/log
    KUBECONFIGS=("")
    KUBECTL="microk8s kubectl"
else
	KUBECONFIGS=("~/.kube/devstageconfig")
fi

echo "Deploying to environment: $ENVIRONMENT"

# Iterate over directories in app/
for dir in $SCRIPT_DIR/app/*/; do
    if [ -d "$dir" ]; then
        DEPLOY_FILE="$dir/${ENVIRONMENT}-deploy.yaml"
        INGRESS_FILE="$dir/${ENVIRONMENT}-ingress.yaml"
        SERVICE_FILE="$dir/${ENVIRONMENT}-service.yaml"

        if [ -f "$DEPLOY_FILE" ] && [ -f "$INGRESS_FILE" ]; then

            echo "Applying deployment for $dir"

			# Iterate over each KUBECONFIG
			for KUBECONFIG in "${KUBECONFIGS[@]}"; do

				if [ -n "$KUBECONFIG" ]; then
					KUBECONFIG="--kubeconfig $KUBECONFIG"
				fi

        	    if ! sed "s/{{DATE_STAMP}}/${DATE_STAMP}/g" $DEPLOY_FILE | $KUBECTL $KUBECONFIG apply -f -;  then
          	    	echo "Failed to apply deployment for $dir"
           	    	exit 1
            	fi

                if [ -f "$SERVICE_FILE" ]; then
                    if ! $KUBECTL $KUBECONFIG apply -f $SERVICE_FILE; then
                        echo "Failed to apply service for $dir"
                        exit 1
                    fi
                fi

                if ! $KUBECTL $KUBECONFIG apply -f $INGRESS_FILE; then
                    echo "Failed to apply ingress for $dir"
                    exit 1
                fi

			done

			echo "Deployment applied successfully for $dir"

        else

            if [ ! -f "$DEPLOY_FILE" ]; then
                echo "No ${ENVIRONMENT}-deploy.yaml found in $dir"
            fi
        
		    if [ ! -f "$INGRESS_FILE" ]; then
                echo "No ${ENVIRONMENT}-ingress.yaml found in $dir"
            fi
        
		fi
    
	fi

done