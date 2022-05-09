FROM kminthein/reconscan:latest
EXPOSE 80 443
COPY entrypoint.sh /home/entrypoint.sh
RUN ["chmod", "+x", "/home/entrypoint.sh"]
ENTRYPOINT ["/home/entrypoint.sh"]
