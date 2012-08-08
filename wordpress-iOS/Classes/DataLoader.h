//
//  OoIURLConnector.h
//  WordPress
//
//  Created by Shakir Ali on 20/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface DataLoader : NSObject<NSURLConnectionDataDelegate>{
    NSMutableData  *jsonData;
    NSURLConnection *dataFeedConnection;
}

@property (nonatomic, retain) NSMutableData *jsonData;
@property (nonatomic, retain) NSURLConnection *dataFeedConnection;
-(void)initConnectionRequest;
-(void)submitURLRequest:(NSURLRequest*)urlRequest;
-(void)cancelRequest;

@end
